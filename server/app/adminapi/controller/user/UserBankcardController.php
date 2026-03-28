<?php

namespace app\adminapi\controller\user;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\user\UserBankcardLists;
use app\adminapi\logic\user\UserBankcardLogic;
use app\adminapi\validate\user\UserBankcardValidate;

/**
 * 银行卡认证管理控制器
 */
class UserBankcardController extends BaseAdminController
{
    /**
     * @notes 查看认证列表
     */
    public function lists()
    {
        return $this->dataLists(new UserBankcardLists());
    }

    /**
     * @notes 审核认证
     */
    public function audit()
    {
        $params = (new UserBankcardValidate())->post()->goCheck('audit');
        $result = UserBankcardLogic::audit($params);
        if (true === $result) {
            return $this->success('审核成功', [], 1, 1);
        }
        return $this->fail(UserBankcardLogic::getError());
    }
}
