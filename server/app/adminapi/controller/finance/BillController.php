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

        // 已提取统计
        $settledWhere = array_merge($where, [['b.status', '=', BillEnum::STATUS_SETTLED]]);
        $totalStats = Bill::alias('b')
            ->join('user u', 'u.id = b.user_id')
            ->where($settledWhere)
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
     * 用户账单明细（管理端查看）
     */
    public function userBills()
    {
        $userId = (int)$this->request->get('user_id', 0);
        if ($userId <= 0) {
            return $this->fail('用户ID不能为空');
        }

        $type = $this->request->get('type', 0);
        $pageNo = (int)$this->request->get('page_no', 1);
        $pageSize = (int)$this->request->get('page_size', 15);

        // 汇总
        $income = Bill::where('user_id', $userId)
            ->where('type', BillEnum::TYPE_INCOME)
            ->where('status', BillEnum::STATUS_SETTLED)
            ->sum('amount');

        $expense = Bill::where('user_id', $userId)
            ->where('type', BillEnum::TYPE_EXPENSE)
            ->whereIn('status', [BillEnum::STATUS_PENDING, BillEnum::STATUS_SETTLED])
            ->sum('amount');

        $balance = round($income - $expense, 2);

        // 列表
        $query = Bill::where('user_id', $userId)
            ->with(['demand' => function ($q) {
                $q->field('id,title');
            }]);

        if ($type > 0) {
            $query->where('type', (int)$type);
        }

        $count = (clone $query)->count();

        $list = $query->order('create_time', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        $billList = [];
        foreach ($list as $item) {
            $title = $item['type'] == BillEnum::TYPE_EXPENSE
                ? '提现'
                : ($item['demand']['title'] ?? '需求结算');
            $billList[] = [
                'id' => $item['id'],
                'title' => $title,
                'time' => date('Y-m-d H:i:s', strtotime($item['create_time'])),
                'amount' => number_format($item['amount'], 2, '.', ''),
                'type' => $item['type'],
                'status' => $item['status'],
                'remark' => $item['remark'] ?? '',
            ];
        }

        return $this->data([
            'income' => number_format($income, 2, '.', ''),
            'expense' => number_format($expense, 2, '.', ''),
            'balance' => number_format($balance, 2, '.', ''),
            'list' => $billList,
            'count' => $count,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
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
