<?php

namespace app\common\enum;

/**
 * 业主认证状态枚举
 */
class UserVerifyEnum
{
    // 待审核
    const STATUS_PENDING = 0;
    // 已通过
    const STATUS_VERIFIED = 1;
    // 已拒绝
    const STATUS_REJECTED = 2;

    /**
     * 获取状态描述
     */
    public static function getStatusDesc($value = true)
    {
        $data = [
            self::STATUS_PENDING => '待审核',
            self::STATUS_VERIFIED => '已通过',
            self::STATUS_REJECTED => '已拒绝',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }
}
