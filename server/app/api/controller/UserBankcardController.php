<?php

namespace app\api\controller;

use app\api\logic\UserBankcardLogic;
use app\api\validate\UserBankcardValidate;

/**
 * 用户银行卡控制器
 */
class UserBankcardController extends BaseApiController
{
    /**
     * 提交银行卡
     */
    public function submit()
    {
        $params = (new UserBankcardValidate())->post()->goCheck('submit');
        $result = UserBankcardLogic::submit($this->userId, $params);
        if ($result === true) {
            return $this->success('提交成功', [], 1, 1);
        }
        return $this->fail(UserBankcardLogic::getError());
    }

    /**
     * 获取银行卡详情
     */
    public function detail()
    {
        $result = UserBankcardLogic::detail($this->userId);
        return $this->data($result);
    }
}
