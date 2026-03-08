<?php

namespace app\common\enum;

/**
 * 小区枚举
 * Class CommunityEnum
 * @package app\common\enum
 */
class CommunityEnum
{
    // 状态
    const STATUS_AUDIT = 0;   // 待审核
    const STATUS_ENABLE = 1;  // 已启用
    const STATUS_REJECT = 2;  // 已拒绝

    /**
     * @notes 获取状态描述
     * @param bool $value
     * @return string|string[]
     */
    public static function getStatusDesc($value = true)
    {
        $data = [
            self::STATUS_AUDIT => '待审核',
            self::STATUS_ENABLE => '已启用',
            self::STATUS_REJECT => '已拒绝',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }
}
