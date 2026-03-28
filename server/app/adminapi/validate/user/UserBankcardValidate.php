<?php

namespace app\adminapi\validate\user;

use app\common\validate\BaseValidate;
use app\common\model\user\UserBankcard;

/**
 * 银行卡认证验证
 */
class UserBankcardValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkUserBankcard',
        'status' => 'require|in:1,2',
        'reject_reason' => 'max:200',
    ];

    protected $message = [
        'id.require' => '认证记录ID不能为空',
        'status.require' => '审核状态不能为空',
        'status.in' => '审核状态不正确',
        'reject_reason.max' => '拒绝原因最多200字',
    ];

    /**
     * @notes 审核场景
     */
    public function sceneAudit()
    {
        return $this->only(['id', 'status', 'reject_reason']);
    }

    /**
     * @notes 检查银行卡认证记录是否存在
     */
    public function checkUserBankcard($value)
    {
        $bankcard = UserBankcard::findOrEmpty($value);
        if ($bankcard->isEmpty()) {
            return '认证记录不存在';
        }
        return true;
    }
}
