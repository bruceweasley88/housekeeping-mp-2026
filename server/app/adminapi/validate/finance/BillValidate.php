<?php

namespace app\adminapi\validate\finance;

use app\common\validate\BaseValidate;
use app\common\model\Bill;

/**
 * 提现管理验证器
 */
class BillValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkBill',
        'remark' => 'require|max:200',
    ];

    protected $message = [
        'id.require' => '账单ID不能为空',
        'remark.require' => '拒绝原因不能为空',
        'remark.max' => '拒绝原因最多200字',
    ];

    /**
     * 审核通过场景
     */
    public function sceneApprove()
    {
        return $this->only(['id']);
    }

    /**
     * 审核拒绝场景
     */
    public function sceneReject()
    {
        return $this->only(['id', 'remark']);
    }

    /**
     * 检查账单记录是否存在
     */
    public function checkBill($value)
    {
        $bill = Bill::findOrEmpty($value);
        if ($bill->isEmpty()) {
            return '账单记录不存在';
        }
        return true;
    }
}
