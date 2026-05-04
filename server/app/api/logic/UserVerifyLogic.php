<?php

namespace app\api\logic;

use app\common\enum\UserVerifyEnum;
use app\common\logic\BaseLogic;
use app\common\model\Community;
use app\common\model\user\UserVerify;
use app\common\service\FileService;

/**
 * 业主认证逻辑层
 */
class UserVerifyLogic extends BaseLogic
{
    /**
     * 提交认证申请
     */
    public static function submit(int $userId, array $params): bool
    {
        try {
            $communityId = $params['community_id'];

            // 验证小区是否存在
            $community = Community::findOrEmpty($communityId);
            if ($community->isEmpty()) {
                throw new \Exception('小区不存在');
            }

            // 检查是否已有已通过的认证（同一小区不允许重复认证）
            $verified = UserVerify::where([
                ['user_id', '=', $userId],
                ['community_id', '=', $communityId],
                ['status', '=', UserVerifyEnum::STATUS_VERIFIED]
            ])->findOrEmpty();
            if (!$verified->isEmpty()) {
                throw new \Exception('您已通过该小区的业主认证');
            }

            // 检查该小区是否有待审核的申请
            $pending = UserVerify::where([
                ['user_id', '=', $userId],
                ['community_id', '=', $communityId],
                ['status', '=', UserVerifyEnum::STATUS_PENDING]
            ])->findOrEmpty();

            if (!$pending->isEmpty()) {
                throw new \Exception('您在该小区有待审核的申请，请等待审核结果');
            }

            // 创建认证申请（模型的设置器会自动处理图片路径）
            UserVerify::create([
                'user_id' => $userId,
                'community_id' => $communityId,
                'idcard_front' => $params['idcard_front'],
                'idcard_back' => $params['idcard_back'],
                'verify_materials' => $params['verify_materials'],
                'status' => UserVerifyEnum::STATUS_PENDING,
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 获取认证详情
     */
    public static function detail(int $userId, int $communityId = 0): array
    {
        $where = [['user_id', '=', $userId]];
        if ($communityId > 0) {
            $where[] = ['community_id', '=', $communityId];
        }

        // 优先查询已通过的认证
        $verified = UserVerify::where($where)
            ->where('status', '=', UserVerifyEnum::STATUS_VERIFIED)
            ->order('id', 'desc')
            ->findOrEmpty();

        if (!$verified->isEmpty()) {
            return self::formatDetail($verified);
        }

        // 没有已通过的，查询最新记录
        $latest = UserVerify::where($where)
            ->order('id', 'desc')
            ->findOrEmpty();

        if ($latest->isEmpty()) {
            return [];
        }

        return self::formatDetail($latest);
    }

    /**
     * 格式化详情数据
     */
    private static function formatDetail($record): array
    {
        $communityName = '';
        if ($record->community) {
            $communityName = $record->community->name;
        }

        return [
            'id' => $record['id'],
            'user_id' => $record['user_id'],
            'community_id' => $record['community_id'],
            'community_name' => $communityName,
            'idcard_front' => $record['idcard_front'],
            'idcard_back' => $record['idcard_back'],
            'verify_materials' => $record['verify_materials'],
            'status' => $record['status'],
            'status_desc' => UserVerifyEnum::getStatusDesc($record['status']),
            'reject_reason' => $record['reject_reason'] ?? '',
            'verified_at' => $record['verified_at'] ?? '',
            'create_time' => $record['create_time'],
        ];
    }

    /**
     * 获取认证历史
     */
    public static function lists(int $userId, array $params): array
    {
        $page = $params['page'] ?? 1;
        $size = $params['size'] ?? 10;

        $count = UserVerify::where('user_id', $userId)->count();
        $list = UserVerify::where('user_id', $userId)
            ->with(['community' => function($query) {
                $query->field('id,name');
            }])
            ->field('id, community_id, status, reject_reason, verified_at, create_time')
            ->page($page, $size)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();

        // 添加状态描述和小区名称
        foreach ($list as &$item) {
            $item['status_desc'] = UserVerifyEnum::getStatusDesc($item['status']);
            $item['community_name'] = $item['community']['name'] ?? '';
            unset($item['community']);
        }

        return [
            'list' => $list,
            'count' => $count,
        ];
    }
}
