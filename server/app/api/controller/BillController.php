<?php

namespace app\api\controller;

use app\api\logic\BillLogic;
use app\api\validate\BillValidate;

/**
 * 账单控制器
 */
class BillController extends BaseApiController
{
    public array $notNeedLogin = ['autoSettle'];

    /**
     * 自动入账（无需登录）
     */
    public function autoSettle()
    {
        BillLogic::autoSettle();
        return $this->success('ok');
    }

    /**
     * 账单列表（需登录）
     */
    public function lists()
    {
        $result = BillLogic::lists($this->userId);
        return $this->data($result);
    }

    /**
     * 结算需求（创建结算支付订单）
     */
    public function settle()
    {
        $params = (new BillValidate())->post()->goCheck('settle');
        $result = BillLogic::settle($this->userId, $params['demand_id']);
        if ($result === false) {
            return $this->fail(BillLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * 提现申请（需登录）
     */
    public function withdraw()
    {
        $params = (new BillValidate())->post()->goCheck('withdraw');
        $result = BillLogic::withdraw($this->userId, $params['amount']);
        if ($result === true) {
            return $this->success('提现申请已提交');
        }
        return $this->fail(BillLogic::getError());
    }
}
