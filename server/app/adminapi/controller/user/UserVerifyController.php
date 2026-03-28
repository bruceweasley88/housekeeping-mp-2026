<?php

namespace app\adminapi\controller\user;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\user\UserVerifyLists;
use app\adminapi\logic\user\UserVerifyLogic;
use app\adminapi\validate\user\UserVerifyValidate;

/**
 * 业主认证管理控制器
 */
class UserVerifyController extends BaseAdminController
{
    /**
     * @notes 查看认证列表
     */
    public function lists()
    {
        return $this->dataLists(new UserVerifyLists());
    }

    /**
     * @notes 审核认证
     */
    public function audit()
    {
        $params = (new UserVerifyValidate())->post()->goCheck('audit');
        $result = UserVerifyLogic::audit($params);
        if (true === $result) {
            return $this->success('审核成功', [], 1, 1);
        }
        return $this->fail(UserVerifyLogic::getError());
    }
}
