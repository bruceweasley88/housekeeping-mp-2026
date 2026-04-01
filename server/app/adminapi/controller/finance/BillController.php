<?php

namespace app\adminapi\controller\finance;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\finance\BillWithdrawLists;
use app\adminapi\logic\finance\BillLogic;
use app\adminapi\validate\finance\BillValidate;

/**
 * 提现管理控制器
 */
class BillController extends BaseAdminController
{
    /**
     * 提现记录列表
     */
    public function lists()
    {
        return $this->dataLists(new BillWithdrawLists());
    }

    /**
     * 审核通过
     */
    public function approve()
    {
        $params = (new BillValidate())->post()->goCheck('approve');
        $result = BillLogic::approve($params['id']);
        if ($result === true) {
            return $this->success('审核通过');
        }
        return $this->fail(BillLogic::getError());
    }

    /**
     * 审核拒绝
     */
    public function reject()
    {
        $params = (new BillValidate())->post()->goCheck('reject');
        $result = BillLogic::reject($params['id'], $params['remark']);
        if ($result === true) {
            return $this->success('已拒绝');
        }
        return $this->fail(BillLogic::getError());
    }
}
