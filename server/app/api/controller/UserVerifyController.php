<?php

namespace app\api\controller;

use app\api\logic\UserVerifyLogic;
use app\api\validate\UserVerifyValidate;

/**
 * 业主认证控制器
 */
class UserVerifyController extends BaseApiController
{
    /**
     * 提交认证申请
     */
    public function submit()
    {
        $params = (new UserVerifyValidate())->post()->goCheck('submit');
        $result = UserVerifyLogic::submit($this->userId, $params);
        if ($result === true) {
            return $this->success('提交成功', [], 1, 1);
        }
        return $this->fail(UserVerifyLogic::getError());
    }

    /**
     * 获取认证详情
     */
    public function detail()
    {
        $result = UserVerifyLogic::detail($this->userId);
        return $this->data($result);
    }

    /**
     * 获取认证历史
     */
    public function lists()
    {
        $params = $this->request->get();
        $result = UserVerifyLogic::lists($this->userId, $params);
        return $this->data($result);
    }
}
