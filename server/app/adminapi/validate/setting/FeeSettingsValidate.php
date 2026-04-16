<?php

namespace app\adminapi\validate\setting;

use app\common\validate\BaseValidate;

/**
 * 手续费设置验证
 * Class FeeSettingsValidate
 * @package app\adminapi\validate\setting
 */
class FeeSettingsValidate extends BaseValidate
{
    protected $rule = [
        'urgent_fee_rate' => 'require|float|egt:0|elt:100',
        'withdraw_fee_rate' => 'require|float|egt:0|elt:100',
        'commission_rate' => 'require|float|egt:0|elt:100',
    ];

    protected $message = [
        'urgent_fee_rate.require' => '请输入紧急手续费率',
        'urgent_fee_rate.float' => '紧急手续费率须为数字',
        'urgent_fee_rate.egt' => '紧急手续费率不能小于0',
        'urgent_fee_rate.elt' => '紧急手续费率不能超过100',

        'withdraw_fee_rate.require' => '请输入提现手续费率',
        'withdraw_fee_rate.float' => '提现手续费率须为数字',
        'withdraw_fee_rate.egt' => '提现手续费率不能小于0',
        'withdraw_fee_rate.elt' => '提现手续费率不能超过100',

        'commission_rate.require' => '请输入平台佣金比例',
        'commission_rate.float' => '平台佣金比例须为数字',
        'commission_rate.egt' => '平台佣金比例不能小于0',
        'commission_rate.elt' => '平台佣金比例不能超过100',
    ];

    public function sceneSetConfig()
    {
        return $this->only(['urgent_fee_rate', 'withdraw_fee_rate', 'commission_rate']);
    }
}
