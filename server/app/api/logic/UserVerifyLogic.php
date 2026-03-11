<?php

namespace app\api\logic;

use app\common\enum\UserVerifyEnum;
use app\common\logic\BaseLogic;
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
            // 检查是否有待审核的申请
            $pending = UserVerify::where([
                ['user_id', '=', $userId],
                ['status', '=', UserVerifyEnum::STATUS_PENDING]
            ])->findOrEmpty();

            if (!$pending->isEmpty()) {
                throw new \Exception('您有待审核的申请，请等待审核结果');
            }

            // 创建认证申请（模型的设置器会自动处理图片路径）
            UserVerify::create([
                'user_id' => $userId,
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
    public static function detail(int $userId): array
    {
        // 优先查询已通过的认证
        $verified = UserVerify::where([
            ['user_id', '=', $userId],
            ['status', '=', UserVerifyEnum::STATUS_VERIFIED]
        ])->order('id', 'desc')->findOrEmpty();

        if (!$verified->isEmpty()) {
            return self::formatDetail($verified);
        }

        // 没有已通过的，查询最新记录
        $latest = UserVerify::where('user_id', $userId)
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
        return [
            'id' => $record['id'],
            'user_id' => $record['user_id'],
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
            ->field('id, status, reject_reason, verified_at, create_time')
            ->page($page, $size)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();

        // 添加状态描述
        foreach ($list as &$item) {
            $item['status_desc'] = UserVerifyEnum::getStatusDesc($item['status']);
        }

        return [
            'list' => $list,
            'count' => $count,
        ];
    }
}
