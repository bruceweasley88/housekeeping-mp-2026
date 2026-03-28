<?php

namespace app\adminapi\validate\user;

use app\common\validate\BaseValidate;

/**
 * 客服验证
 */
class KefuValidate extends BaseValidate
{
    protected $rule = [
        'id'      => 'require',
        'user_id' => 'require|integer',
        'status'  => 'require|in:0,1',
    ];

    protected $message = [
        'id.require'      => '客服ID不能为空',
        'user_id.require' => '用户ID不能为空',
        'user_id.integer' => '用户ID格式不正确',
        'status.require'  => '状态不能为空',
        'status.in'       => '状态值不正确',
    ];

    public function sceneAdd()
    {
        return $this->only(['user_id']);
    }

    public function sceneRemove()
    {
        return $this->only(['id']);
    }

    public function sceneStatus()
    {
        return $this->only(['id', 'status']);
    }
}
