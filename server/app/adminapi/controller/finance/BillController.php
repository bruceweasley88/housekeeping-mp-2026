<?php

namespace app\adminapi\controller\finance;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\finance\BillWithdrawLists;
use app\adminapi\logic\finance\BillLogic;
use app\adminapi\validate\finance\BillValidate;
use app\common\enum\BillEnum;
use app\common\model\Bill;

/**
 * 提现管理控制器
 */
class BillController extends BaseAdminController
{
    /**
     * 提现统计汇总
     */
    public function summary()
    {
        $params = $this->request->only([
            'user_info' => '',
            'start_time' => '',
            'end_time' => '',
        ]);

        $where = [];
        $where[] = ['b.type', '=', BillEnum::TYPE_EXPENSE];

        if (!empty($params['user_info'])) {
            $where[] = ['u.nickname|u.mobile', 'like', '%' . $params['user_info'] . '%'];
        }
        if (!empty($params['start_time'])) {
            $where[] = ['b.create_time', '>=', strtotime($params['start_time'])];
        }
        if (!empty($params['end_time'])) {
            $where[] = ['b.create_time', '<=', strtotime($params['end_time'])];
        }

        // 全部提现统计
        $totalStats = Bill::alias('b')
            ->join('user u', 'u.id = b.user_id')
            ->where($where)
            ->field('COUNT(*) as count, COALESCE(SUM(b.amount), 0) as amount')
            ->find();

        // 已拒绝统计
        $rejectedWhere = array_merge($where, [['b.status', '=', BillEnum::STATUS_REJECTED]]);
        $rejectedStats = Bill::alias('b')
            ->join('user u', 'u.id = b.user_id')
            ->where($rejectedWhere)
            ->field('COUNT(*) as count, COALESCE(SUM(b.amount), 0) as amount')
            ->find();

        return $this->data([
            'total' => [
                'count' => (int)($totalStats['count'] ?? 0),
                'amount' => round((float)($totalStats['amount'] ?? 0), 2),
            ],
            'rejected' => [
                'count' => (int)($rejectedStats['count'] ?? 0),
                'amount' => round((float)($rejectedStats['amount'] ?? 0), 2),
            ],
        ]);
    }

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
