<?php

namespace app\adminapi\controller\finance;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\finance\BillWithdrawLists;
use app\adminapi\logic\finance\BillLogic;
use app\adminapi\validate\finance\BillValidate;
use app\common\enum\BillEnum;
use app\common\model\Bill;
use app\common\service\ConfigService;

/**
 * 提现管理控制器
 */
class BillController extends BaseAdminController
{
    /**
     * 平台提现统计（按周/月/年）
     */
    public function platformStats()
    {
        $period = $this->request->get('period', 'month');
        $where = $this->buildPeriodWhere($period);
        $where[] = ['b.type', '=', BillEnum::TYPE_EXPENSE];

        // 已提取
        $settledWhere = array_merge($where, [['b.status', '=', BillEnum::STATUS_SETTLED]]);
        $settled = Bill::alias('b')
            ->where($settledWhere)
            ->field('COUNT(*) as count, COALESCE(SUM(b.amount), 0) as amount')
            ->find();

        // 已拒绝
        $rejectedWhere = array_merge($where, [['b.status', '=', BillEnum::STATUS_REJECTED]]);
        $rejected = Bill::alias('b')
            ->where($rejectedWhere)
            ->field('COUNT(*) as count, COALESCE(SUM(b.amount), 0) as amount')
            ->find();

        // 待审核
        $pendingWhere = array_merge($where, [['b.status', '=', BillEnum::STATUS_PENDING]]);
        $pending = Bill::alias('b')
            ->where($pendingWhere)
            ->field('COUNT(*) as count, COALESCE(SUM(b.amount), 0) as amount')
            ->find();

        // 佣金收入 = 已提取金额 × 手续费率
        $feeRate = ConfigService::get('fee', 'withdraw_fee_rate', 3);
        $settledAmount = round((float)($settled['amount'] ?? 0), 2);
        $commission = round($settledAmount * $feeRate / 100, 2);

        return $this->data([
            'settled_count' => (int)($settled['count'] ?? 0),
            'settled_amount' => $settledAmount,
            'rejected_count' => (int)($rejected['count'] ?? 0),
            'rejected_amount' => round((float)($rejected['amount'] ?? 0), 2),
            'pending_count' => (int)($pending['count'] ?? 0),
            'pending_amount' => round((float)($pending['amount'] ?? 0), 2),
            'commission_amount' => $commission,
        ]);
    }

    /**
     * 根据周期参数构建时间 WHERE 条件
     */
    private function buildPeriodWhere(string $period): array
    {
        $now = time();
        switch ($period) {
            case 'week':
                $start = strtotime('monday this week', $now);
                break;
            case 'year':
                $start = mktime(0, 0, 0, 1, 1, date('Y', $now));
                break;
            default: // month
                $start = mktime(0, 0, 0, date('m', $now), 1, date('Y', $now));
                break;
        }
        return [['b.create_time', '>=', $start]];
    }

    /**
     * 用户提现统计（按周/月/年，按用户聚合）
     */
    public function userStats()
    {
        $period = $this->request->get('period', 'month');
        $pageNo = (int)$this->request->get('page_no', 1);
        $pageSize = (int)$this->request->get('page_size', 15);

        $where = $this->buildPeriodWhere($period);
        $where[] = ['b.type', '=', BillEnum::TYPE_EXPENSE];

        // 按用户聚合：已提取笔数/金额、已拒绝笔数/金额
        $query = Bill::alias('b')
            ->join('user u', 'u.id = b.user_id')
            ->where($where)
            ->group('b.user_id')
            ->field('b.user_id, u.nickname, u.mobile')
            ->field('SUM(CASE WHEN b.status = ' . BillEnum::STATUS_SETTLED . ' THEN 1 ELSE 0 END) as settled_count')
            ->field('COALESCE(SUM(CASE WHEN b.status = ' . BillEnum::STATUS_SETTLED . ' THEN b.amount ELSE 0 END), 0) as settled_amount')
            ->field('SUM(CASE WHEN b.status = ' . BillEnum::STATUS_REJECTED . ' THEN 1 ELSE 0 END) as rejected_count')
            ->field('COALESCE(SUM(CASE WHEN b.status = ' . BillEnum::STATUS_REJECTED . ' THEN b.amount ELSE 0 END), 0) as rejected_amount')
            ->order('settled_amount', 'desc');

        $count = count($query->group('b.user_id')->select());
        $list = (clone $query)->page($pageNo, $pageSize)->select()->toArray();

        foreach ($list as &$item) {
            $item['settled_count'] = (int)$item['settled_count'];
            $item['settled_amount'] = round((float)$item['settled_amount'], 2);
            $item['rejected_count'] = (int)$item['rejected_count'];
            $item['rejected_amount'] = round((float)$item['rejected_amount'], 2);
        }

        return $this->data([
            'list' => $list,
            'count' => $count,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * 用户提现统计明细
     */
    public function userStatsDetail()
    {
        $userId = (int)$this->request->get('user_id', 0);
        if ($userId <= 0) {
            return $this->fail('用户ID不能为空');
        }

        $period = $this->request->get('period', 'month');
        $where = $this->buildPeriodWhere($period);
        $where[] = ['b.type', '=', BillEnum::TYPE_EXPENSE];
        $where[] = ['b.user_id', '=', $userId];

        $list = Bill::alias('b')
            ->join('user u', 'u.id = b.user_id')
            ->where($where)
            ->field('b.id, b.bill_no, b.amount, b.status, b.remark, b.create_time, u.nickname, u.mobile')
            ->order('b.create_time', 'desc')
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $item['status_desc'] = BillEnum::getStatusDesc($item['status']);
            $item['create_time_desc'] = date('Y-m-d H:i:s', strtotime($item['create_time']));
            $item['amount'] = round((float)$item['amount'], 2);
        }

        return $this->data($list);
    }

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
