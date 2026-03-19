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
    ];

    protected $message = [
        'demand_id.require' => '需求ID不能为空',
        'demand_id.integer' => '需求ID必须是整数',
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
}
