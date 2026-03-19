<?php

namespace app\common\enum;

/**
 * 账单枚举
 */
class BillEnum
{
    // 类型常量
    const TYPE_INCOME = 1;   // 收入
    const TYPE_EXPENSE = 2;  // 支出

    // 状态常量
    const STATUS_PENDING = 1;   // 待入账
    const STATUS_SETTLED = 2;   // 已入账

    /**
     * 获取类型描述
     */
    public static function getTypeDesc($value = true)
    {
        $data = [
            self::TYPE_INCOME => '收入',
            self::TYPE_EXPENSE => '支出',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }

    /**
     * 获取状态描述
     */
    public static function getStatusDesc($value = true)
    {
        $data = [
            self::STATUS_PENDING => '待入账',
            self::STATUS_SETTLED => '已入账',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }
}
