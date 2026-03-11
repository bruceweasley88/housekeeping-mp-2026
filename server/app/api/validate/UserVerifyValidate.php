<?php

namespace app\api\validate;

use app\common\validate\BaseValidate;

/**
 * 业主认证验证器
 */
class UserVerifyValidate extends BaseValidate
{
    protected $rule = [
        'idcard_front' => 'require',
        'idcard_back' => 'require',
        'verify_materials' => 'require|array|min:1',
    ];

    protected $message = [
        'idcard_front.require' => '请上传身份证正面照',
        'idcard_back.require' => '请上传身份证背面照',
        'verify_materials.require' => '请上传辅助证明材料',
        'verify_materials.array' => '证明材料格式错误',
        'verify_materials.min' => '至少上传一张辅助证明材料',
    ];

    /**
     * 提交认证场景
     */
    public function sceneSubmit()
    {
        return $this->only(['idcard_front', 'idcard_back', 'verify_materials']);
    }
}
