<?php

namespace app\api\validate;

use app\common\validate\BaseValidate;

/**
 * 需求分类验证器
 */
class DemandCategoryValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
    ];

    protected $message = [
        'id.require' => '分类ID不能为空',
        'id.integer' => '分类ID必须是整数',
    ];
}
