<?php

namespace app\common\enum;

/**
 * 需求金额类型枚举
 */
class DemandPriceTypeEnum
{
    // 类型常量
    const TYPE_HOURLY = 1;   // 按小时
    const TYPE_FIXED = 2;    // 按次
    const TYPE_RANGE = 3;    // 按范围

    /**
     * 获取类型描述
     */
    public static function getTypeDesc($value = true)
    {
        $data = [
            self::TYPE_HOURLY => '按小时',
            self::TYPE_FIXED => '按次',
            self::TYPE_RANGE => '按范围',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }
}
