<?php

namespace app\api\logic;

use app\common\enum\DemandEnum;
use app\common\enum\DemandPriceTypeEnum;
use app\common\enum\UserVerifyEnum;
use app\common\logic\BaseLogic;
use app\common\model\Demand;
use app\common\model\DemandAccept;
use app\common\service\ConfigService;
use think\facade\Db;
use app\api\logic\NoticeLogic;

/**
 * 需求逻辑层
 */
class DemandLogic extends BaseLogic
{
    /**
     * 需求列表（免登录）
     */
    public static function lists(array $params): array
    {
        $where = [
            ['status', '=', DemandEnum::STATUS_PENDING], // 默认只显示待接单
        ];

        // 分类筛选
        if (!empty($params['category_id'])) {
            $where[] = ['category_id', '=', $params['category_id']];
        }

        // 小区筛选
        if (!empty($params['community_id'])) {
            $where[] = ['community_id', '=', $params['community_id']];
        }

        // 状态筛选（默认为待接单）
        if (isset($params['status']) && $params['status'] !== '' && $params['status'] !== null) {
            $where[0] = ['status', '=', $params['status']];
        }

        // 关键词搜索
        if (!empty($params['keyword'])) {
            $where[] = ['title|description', 'like', '%' . $params['keyword'] . '%'];
        }

        $page = $params['page_no'] ?? 1;
        $size = $params['page_size'] ?? 10;

        $count = Demand::where($where)->count();
        $list = Demand::where($where)
            ->with(['category' => function($query) {
                $query->field('id,name');
            }, 'publisher' => function($query) {
                $query->field('id,nickname,avatar');
            }, 'community' => function($query) {
                $query->field('id,name');
            }])
            ->field('id,demand_no,title,description,images,amount,price_type,hours,hour_price,min_amount,max_amount,is_urgent,status,create_time,user_id,category_id,community_id,address')
            ->page($page, $size)
            ->order(['is_urgent' => 'desc', 'create_time' => 'desc'])
            ->select()
            ->toArray();

        // 格式化数据
        foreach ($list as &$item) {
            $item['status_desc'] = DemandEnum::getStatusDesc($item['status']);
            $item['price_type_desc'] = DemandPriceTypeEnum::getTypeDesc($item['price_type']);
            $item['price_display'] = self::formatPriceDisplay($item);
            $item['category_name'] = $item['category']['name'] ?? '';
            $item['community_name'] = $item['community']['name'] ?? '';
            $item['user_info'] = [
                'id' => $item['publisher']['id'] ?? 0,
                'nickname' => $item['publisher']['nickname'] ?? '',
                'avatar' => $item['publisher']['avatar'] ?? '',
            ];
            unset($item['publisher'], $item['category'], $item['community']);
        }

        return [
            'list' => $list,
            'count' => $count,
            'page_no' => $page,
            'page_size' => $size,
        ];
    }

    /**
     * 需求详情（免登录，但有权限控制）
     */
    public static function detail(int $id, int $userId = 0): array
    {
        $demand = Demand::where('id', $id)
            ->with([
                'category' => function($query) {
                    $query->field('id,name,icon');
                },
                'publisher' => function($query) {
                    $query->field('id,nickname,avatar,mobile');
                },
                'acceptor' => function($query) {
                    $query->field('id,nickname,avatar,mobile');
                },
                'community' => function($query) {
                    $query->field('id,name,address');
                }
            ])
            ->findOrEmpty();

        if ($demand->isEmpty()) {
            self::setError('需求不存在');
            return [];
        }

        $data = $demand->toArray();

        // 判断当前用户角色
        $role = self::getUserRole($data, $userId);

        // 权限检查：陌生人只能查看待接单状态
        if ($role === 'stranger' && $data['status'] != DemandEnum::STATUS_PENDING) {
            self::setError('该需求不可查看');
            return [];
        }

        // 状态描述
        $data['status_desc'] = DemandEnum::getStatusDesc($data['status']);
        $data['price_type_desc'] = DemandPriceTypeEnum::getTypeDesc($data['price_type']);
        $data['price_display'] = self::formatPriceDisplay($data);

        // 角色标识
        $data['is_owner'] = ($role === 'publisher');
        $data['is_accepter'] = ($role === 'acceptor');
        $data['can_accept'] = ($userId > 0 && $data['status'] == DemandEnum::STATUS_PENDING && $role === 'stranger');

        // 状态提示
        $roleForTips = $role;
        if ($role === 'stranger') {
            $roleForTips = 'publisher';
        }
        $data['status_tips'] = DemandEnum::getStatusTips($data['status'], $roleForTips);

        // 用户信息
        $data['user_info'] = [
            'id' => $data['publisher']['id'] ?? 0,
            'nickname' => $data['publisher']['nickname'] ?? '',
            'avatar' => $data['publisher']['avatar'] ?? '',
        ];

        // 分类信息
        $data['category'] = [
            'id' => $data['category']['id'] ?? 0,
            'name' => $data['category']['name'] ?? '',
        ];
        $data['category_name'] = $data['category']['name'];

        // 小区名称
        $data['community_name'] = $data['community']['name'] ?? '';

        // 处理联系方式显示
        if ($role === 'stranger' && $data['show_phone'] == 0) {
            $data['contact_phone'] = self::hideMobile($data['contact_phone']);
        }

        // 承接信息
        $data['accept_info'] = null;
        if ($data['status'] >= DemandEnum::STATUS_ONGOING && !empty($data['acceptor'])) {
            $data['accept_info'] = [
                'id' => $data['acceptor']['id'],
                'nickname' => $data['acceptor']['nickname'],
                'avatar' => $data['acceptor']['avatar'],
            ];
            // 发布者可见承接者手机号
            if ($role === 'publisher') {
                $data['accept_info']['mobile'] = self::hideMobile($data['acceptor']['mobile']);
            }
        }

        // 承接时间格式化
        if (!empty($data['accept_time'])) {
            $data['accept_time_format'] = date('Y.m.d H:i', $data['accept_time']);
        }

        return $data;
    }

    /**
     * 发布需求（需登录）
     */
    public static function publish(int $userId, array $params): array|false
    {
        try {
            Db::startTrans();

            // 计算金额
            $amount = self::calculateAmount($params);
            if ($amount === false) {
                return false;
            }

            // 计算服务费率
            $serviceRate = 0;
            if (!empty($params['is_urgent']) && $params['is_urgent'] == 1) {
                $serviceRate = ConfigService::get('fee', 'urgent_fee_rate', 3);
            }

            // 生成需求编号
            $demandNo = Demand::generateDemandNo();

            // 创建需求
            $demand = Demand::create([
                'demand_no' => $demandNo,
                'user_id' => $userId,
                'category_id' => $params['category_id'],
                'title' => $params['title'],
                'description' => $params['description'],
                'images' => $params['images'] ?? [],
                'amount' => $amount,
                'price_type' => $params['price_type'],
                'hours' => $params['hours'] ?? null,
                'hour_price' => $params['hour_price'] ?? null,
                'min_amount' => $params['min_amount'] ?? null,
                'max_amount' => $params['max_amount'] ?? null,
                'community_id' => $params['community_id'],
                'address' => $params['address'],
                'contact_name' => $params['contact_name'],
                'contact_phone' => $params['contact_phone'],
                'show_phone' => $params['show_phone'] ?? 0,
                'is_urgent' => $params['is_urgent'] ?? 0,
                'service_rate' => $serviceRate,
                'status' => DemandEnum::STATUS_PENDING,
            ]);

            Db::commit();
            return [
                'id' => $demand->id,
                'demand_no' => $demandNo,
            ];
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 编辑需求（需登录）
     */
    public static function edit(int $userId, array $params): bool
    {
        try {
            $demand = Demand::where('id', $params['id'])->findOrEmpty();
            if ($demand->isEmpty()) {
                throw new \Exception('需求不存在');
            }

            // 验证是否是发布者
            if ($demand->user_id != $userId) {
                throw new \Exception('无权编辑该需求');
            }

            // 验证状态（只有待接单状态可编辑）
            if ($demand->status != DemandEnum::STATUS_PENDING) {
                throw new \Exception('当前状态不允许编辑');
            }

            // 计算金额
            $amount = self::calculateAmount($params);
            if ($amount === false) {
                return false;
            }

            // 计算服务费率
            $serviceRate = 0;
            if (!empty($params['is_urgent']) && $params['is_urgent'] == 1) {
                $serviceRate = ConfigService::get('fee', 'urgent_fee_rate', 3);
            }

            // 更新需求
            $demand->save([
                'category_id' => $params['category_id'],
                'title' => $params['title'],
                'description' => $params['description'],
                'images' => $params['images'] ?? [],
                'amount' => $amount,
                'price_type' => $params['price_type'],
                'hours' => $params['hours'] ?? null,
                'hour_price' => $params['hour_price'] ?? null,
                'min_amount' => $params['min_amount'] ?? null,
                'max_amount' => $params['max_amount'] ?? null,
                'community_id' => $params['community_id'],
                'address' => $params['address'],
                'contact_name' => $params['contact_name'],
                'contact_phone' => $params['contact_phone'],
                'show_phone' => $params['show_phone'] ?? 0,
                'is_urgent' => $params['is_urgent'] ?? 0,
                'service_rate' => $serviceRate,
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 承接需求（需登录，需业主认证）
     */
    public static function accept(int $userId, int $demandId): bool
    {
        try {
            Db::startTrans();

            // 检查业主认证状态
            $verified = \app\common\model\user\UserVerify::where([
                ['user_id', '=', $userId],
                ['status', '=', UserVerifyEnum::STATUS_VERIFIED]
            ])->findOrEmpty();

            if ($verified->isEmpty()) {
                throw new \Exception('请先完成业主认证');
            }

            $demand = Demand::where('id', $demandId)->findOrEmpty();
            if ($demand->isEmpty()) {
                throw new \Exception('需求不存在');
            }

            // 验证状态
            if ($demand->status != DemandEnum::STATUS_PENDING) {
                throw new \Exception('该需求已被承接或已关闭');
            }

            // 验证不能承接自己发布的需求
            if ($demand->user_id == $userId) {
                throw new \Exception('不能承接自己发布的需求');
            }

            // 更新需求状态
            $demand->save([
                'status' => DemandEnum::STATUS_ONGOING,
                'accept_user_id' => $userId,
                'accept_time' => time(),
            ]);

            // 创建承接记录
            DemandAccept::create([
                'demand_id' => $demandId,
                'user_id' => $userId,
                'status' => DemandAccept::STATUS_ACCEPTING,
            ]);

            // 发送系统公告：通知发布者
            NoticeLogic::addNotice(
                $demand->user_id,
                '您的需求已被承接',
                '您的需求已被承接，可在需求详情查看承接人信息，立即进行线上沟通，与他约定具体的需求内容。',
                'demand',
                $demand->id
            );

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 取消承接（需登录）
     */
    public static function cancelAccept(int $userId, int $demandId): bool
    {
        try {
            Db::startTrans();

            $demand = Demand::where('id', $demandId)->findOrEmpty();
            if ($demand->isEmpty()) {
                throw new \Exception('需求不存在');
            }

            // 验证状态
            if ($demand->status != DemandEnum::STATUS_ONGOING) {
                throw new \Exception('当前状态不允许取消');
            }

            // 验证是否是承接者
            if ($demand->accept_user_id != $userId) {
                throw new \Exception('无权操作');
            }

            // 更新需求状态
            $demand->save([
                'status' => DemandEnum::STATUS_PENDING,
                'accept_user_id' => null,
                'accept_time' => null,
            ]);

            // 更新承接记录状态
            DemandAccept::where([
                ['demand_id', '=', $demandId],
                ['user_id', '=', $userId],
                ['status', '=', DemandAccept::STATUS_ACCEPTING]
            ])->update([
                'status' => DemandAccept::STATUS_CANCELLED,
                'update_time' => time()
            ]);

            // 发送系统公告：通知发布者
            NoticeLogic::addNotice(
                $demand->user_id,
                '您的需求承接已取消',
                '您的需求承接已取消，可重新等待他人承接或查看其他需求。',
                'demand',
                $demand->id
            );

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 确认完成（需登录）
     */
    public static function finish(int $userId, int $demandId): bool
    {
        try {
            $demand = Demand::where('id', $demandId)->findOrEmpty();
            if ($demand->isEmpty()) {
                throw new \Exception('需求不存在');
            }

            // 验证状态
            if ($demand->status != DemandEnum::STATUS_ONGOING) {
                throw new \Exception('当前状态不允许确认完成');
            }

            // 验证是否是发布者
            if ($demand->user_id != $userId) {
                throw new \Exception('无权操作');
            }

            // 更新需求状态
            $demand->save([
                'status' => DemandEnum::STATUS_COMPLETED,
            ]);

            // 更新承接记录状态
            DemandAccept::where([
                ['demand_id', '=', $demandId],
                ['status', '=', DemandAccept::STATUS_ACCEPTING]
            ])->update([
                'status' => DemandAccept::STATUS_COMPLETED,
                'update_time' => time()
            ]);

            // 发送系统公告：通知承接者
            NoticeLogic::addNotice(
                $demand->accept_user_id,
                '需求已确认完成',
                '您承接的需求已确认完成，可在需求详情查看完成信息，等待发布者结算。',
                'demand',
                $demand->id
            );

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 调整金额（需登录）
     */
    public static function adjustAmount(int $userId, int $demandId, float $amount): bool
    {
        try {
            $demand = Demand::where('id', $demandId)->findOrEmpty();
            if ($demand->isEmpty()) {
                throw new \Exception('需求不存在');
            }

            // 验证是否是发布者
            if ($demand->user_id != $userId) {
                throw new \Exception('无权操作');
            }

            // 验证状态（进行中或已完成可调整）
            if (!in_array($demand->status, [DemandEnum::STATUS_ONGOING, DemandEnum::STATUS_COMPLETED])) {
                throw new \Exception('当前状态不允许调整金额');
            }

            // 更新金额
            $demand->save([
                'amount' => $amount,
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 我的需求列表（需登录）- 合并接口
     * @param int $userId 用户ID
     * @param array $params 参数（type: publish/accept, status, page_no, page_size）
     */
    public static function myList(int $userId, array $params): array
    {
        $type = $params['type'] ?? 'publish';
        $page = $params['page_no'] ?? 1;
        $size = $params['page_size'] ?? 10;

        // 根据 type 构建查询条件
        $where = [];
        if ($type === 'publish') {
            $where[] = ['user_id', '=', $userId];
        } else {
            $where[] = ['accept_user_id', '=', $userId];
        }

        // 状态筛选
        if (isset($params['status']) && $params['status'] !== '' && $params['status'] !== null) {
            $where[] = ['status', '=', $params['status']];
        }

        $count = Demand::where($where)->count();
        $list = Demand::where($where)
            ->with(['category' => function($query) {
                $query->field('id,name');
            }, 'publisher' => function($query) {
                $query->field('id,nickname,avatar');
            }, 'community' => function($query) {
                $query->field('id,name');
            }])
            ->field('id,demand_no,title,description,images,amount,price_type,hours,hour_price,min_amount,max_amount,status,is_urgent,accept_time,create_time,category_id,community_id,address,user_id')
            ->page($page, $size)
            ->order([$type === 'publish' ? 'create_time' : 'accept_time' => 'desc'])
            ->select()
            ->toArray();

        // 统一格式化
        foreach ($list as &$item) {
            $item['status_desc'] = DemandEnum::getStatusDesc($item['status']);
            $item['price_type_desc'] = DemandPriceTypeEnum::getTypeDesc($item['price_type']);
            $item['price_display'] = self::formatPriceDisplay($item);
            $item['category_name'] = $item['category']['name'] ?? '';
            $item['community_name'] = $item['community']['name'] ?? '';

            // 统一使用 user_info 返回发布者信息
            $item['user_info'] = [
                'id' => $item['publisher']['id'] ?? 0,
                'nickname' => $item['publisher']['nickname'] ?? '',
                'avatar' => $item['publisher']['avatar'] ?? '',
            ];

            if (!empty($item['accept_time'])) {
                $item['accept_time_format'] = date('Y.m.d H:i', $item['accept_time']);
            }

            unset($item['category'], $item['publisher'], $item['community']);
        }

        return [
            'list' => $list,
            'count' => $count,
            'page_no' => $page,
            'page_size' => $size,
        ];
    }

    /**
     * 计算金额
     */
    private static function calculateAmount(array $params): float|false
    {
        $priceType = $params['price_type'];

        switch ($priceType) {
            case DemandPriceTypeEnum::TYPE_HOURLY:
                // 按小时：金额 = 每小时金额 × 总小时
                return (float)($params['hour_price'] * $params['hours']);
            case DemandPriceTypeEnum::TYPE_FIXED:
                // 按次：金额 = 输入的金额
                return (float)$params['amount'];
            case DemandPriceTypeEnum::TYPE_RANGE:
                // 按范围：金额 = 最小金额
                return (float)$params['min_amount'];
            default:
                self::setError('金额类型错误');
                return false;
        }
    }

    /**
     * 格式化价格显示
     */
    private static function formatPriceDisplay(array $data): string
    {
        $priceType = $data['price_type'] ?? 2;

        switch ($priceType) {
            case DemandPriceTypeEnum::TYPE_HOURLY:
                return '¥' . ($data['hour_price'] ?? 0) . '/小时 × ' . ($data['hours'] ?? 0) . '小时';
            case DemandPriceTypeEnum::TYPE_FIXED:
                return '¥' . ($data['amount'] ?? 0) . '/次';
            case DemandPriceTypeEnum::TYPE_RANGE:
                return '¥' . ($data['min_amount'] ?? 0) . ' ~ ¥' . ($data['max_amount'] ?? 0);
            default:
                return '¥' . ($data['amount'] ?? 0);
        }
    }

    /**
     * 判断用户角色
     */
    private static function getUserRole(array $demand, int $userId): string
    {
        if ($userId == 0) {
            return 'stranger';
        }
        if ($demand['user_id'] == $userId) {
            return 'publisher';
        }
        if (!empty($demand['accept_user_id']) && $demand['accept_user_id'] == $userId) {
            return 'acceptor';
        }
        return 'stranger';
    }

    /**
     * 删除需求（需登录）
     */
    public static function delete(int $userId, int $id): bool
    {
        try {
            $demand = Demand::where('id', $id)->findOrEmpty();
            if ($demand->isEmpty()) {
                throw new \Exception('需求不存在');
            }

            // 验证是否是发布者
            if ($demand->user_id != $userId) {
                throw new \Exception('无权删除该需求');
            }

            // 验证状态（只有待接单状态可删除）
            if ($demand->status != DemandEnum::STATUS_PENDING) {
                throw new \Exception('当前状态不允许删除');
            }

            // 软删除
            $demand->delete();
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 隐藏手机号中间4位
     */
    private static function hideMobile(string $mobile): string
    {
        if (strlen($mobile) != 11) {
            return $mobile;
        }
        return substr($mobile, 0, 3) . '****' . substr($mobile, -4);
    }
}
