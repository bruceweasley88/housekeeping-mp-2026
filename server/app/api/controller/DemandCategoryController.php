<?php

namespace app\api\controller;

use app\api\logic\DemandCategoryLogic;

/**
 * 需求分类控制器
 */
class DemandCategoryController extends BaseApiController
{
    public array $notNeedLogin = ['lists'];

    /**
     * 需求分类列表（免登录）
     */
    public function lists()
    {
        $result = DemandCategoryLogic::lists();
        return $this->data($result);
    }
}
