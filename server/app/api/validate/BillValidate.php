<?php

namespace app\api\validate;

use app\common\validate\BaseValidate;

/**
 * 账单验证器
 */
class BillValidate extends BaseValidate
{
    protected $rule = [
        'demand_id' => 'require|integer',
        'amount' => 'require|float|gt:0',
    ];

    protected $message = [
        'demand_id.require' => '需求ID不能为空',
        'demand_id.integer' => '需求ID必须是整数',
        'amount.require' => '提现金额不能为空',
        'amount.float' => '提现金额格式不正确',
        'amount.gt' => '提现金额必须大于0',
    ];

    /**
     * 账单列表场景（无需参数验证）
     */
    public function sceneLists()
    {
        return $this->only([]);
    }

    /**
     * 结算场景
     */
    public function sceneSettle()
    {
        return $this->only(['demand_id']);
    }

    /**
     * 提现场景
     */
    public function sceneWithdraw()
    {
        return $this->only(['amount']);
    }
}
