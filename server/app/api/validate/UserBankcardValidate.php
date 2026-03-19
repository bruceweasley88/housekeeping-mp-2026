<?php

namespace app\api\validate;

use app\common\validate\BaseValidate;

/**
 * 用户银行卡验证器
 */
class UserBankcardValidate extends BaseValidate
{
    protected $rule = [
        'real_name' => 'require',
        'bankcard_image' => 'require',
    ];

    protected $message = [
        'real_name.require' => '请填写真实姓名',
        'bankcard_image.require' => '请上传银行卡照片',
    ];

    /**
     * 提交银行卡场景
     */
    public function sceneSubmit()
    {
        return $this->only(['real_name', 'bankcard_image']);
    }
}
