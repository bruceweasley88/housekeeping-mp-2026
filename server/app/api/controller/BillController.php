<?php

namespace app\api\controller;

use app\api\logic\BillLogic;
use app\api\validate\BillValidate;

/**
 * 账单控制器
 */
class BillController extends BaseApiController
{
    public array $notNeedLogin = [];

    /**
     * 账单列表（需登录）
     */
    public function lists()
    {
        $result = BillLogic::lists($this->userId);
        return $this->data($result);
    }

    /**
     * 结算需求（需登录）
     */
    public function settle()
    {
        $params = (new BillValidate())->post()->goCheck('settle');
        $result = BillLogic::settle($this->userId, $params['demand_id']);
        if ($result === true) {
            return $this->success('结算成功', [], 1, 1);
        }
        return $this->fail(BillLogic::getError());
    }
}
