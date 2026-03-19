<?php

namespace app\api\logic;

use app\common\enum\UserBankcardEnum;
use app\common\logic\BaseLogic;
use app\common\model\user\UserBankcard;

/**
 * 用户银行卡逻辑层
 */
class UserBankcardLogic extends BaseLogic
{
    /**
     * 提交银行卡
     */
    public static function submit(int $userId, array $params): bool
    {
        try {
            // 检查是否有待审核的申请
            $pending = UserBankcard::where([
                ['user_id', '=', $userId],
                ['status', '=', UserBankcardEnum::STATUS_PENDING]
            ])->findOrEmpty();

            if (!$pending->isEmpty()) {
                throw new \Exception('您有待审核的申请，请等待审核结果');
            }

            // 创建银行卡申请（模型的设置器会自动处理图片路径）
            UserBankcard::create([
                'user_id' => $userId,
                'real_name' => $params['real_name'],
                'bankcard_image' => $params['bankcard_image'],
                'status' => UserBankcardEnum::STATUS_PENDING,
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 获取银行卡详情
     */
    public static function detail(int $userId): array
    {
        // 优先查询已通过的银行卡
        $verified = UserBankcard::where([
            ['user_id', '=', $userId],
            ['status', '=', UserBankcardEnum::STATUS_VERIFIED]
        ])->order('id', 'desc')->findOrEmpty();

        if (!$verified->isEmpty()) {
            return self::formatDetail($verified);
        }

        // 没有已通过的，查询最新记录
        $latest = UserBankcard::where('user_id', $userId)
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
            'real_name' => $record['real_name'],
            'bankcard_image' => $record['bankcard_image'],
            'status' => $record['status'],
            'status_desc' => UserBankcardEnum::getStatusDesc($record['status']),
            'reject_reason' => $record['reject_reason'] ?? '',
            'verified_at' => $record['verified_at'] ?? '',
            'create_time' => $record['create_time'],
        ];
    }
}
