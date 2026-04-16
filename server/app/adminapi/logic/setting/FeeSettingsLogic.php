<?php

namespace app\adminapi\logic\setting;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

/**
 * 手续费设置逻辑
 * Class FeeSettingsLogic
 * @package app\adminapi\logic\setting
 */
class FeeSettingsLogic extends BaseLogic
{
    /**
     * @notes 获取手续费设置
     */
    public static function getConfig(): array
    {
        return [
            'urgent_fee_rate' => ConfigService::get('fee', 'urgent_fee_rate', 3),
            'withdraw_fee_rate' => ConfigService::get('fee', 'withdraw_fee_rate', 3),
            'commission_rate' => ConfigService::get('fee', 'commission_rate', 0),
        ];
    }

    /**
     * @notes 设置手续费
     */
    public static function setConfig(array $params): void
    {
        ConfigService::set('fee', 'urgent_fee_rate', $params['urgent_fee_rate']);
        ConfigService::set('fee', 'withdraw_fee_rate', $params['withdraw_fee_rate']);
        ConfigService::set('fee', 'commission_rate', $params['commission_rate']);
    }
}
