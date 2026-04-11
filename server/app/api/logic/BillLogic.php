<?php

namespace app\api\logic;

use app\common\enum\BillEnum;
use app\common\enum\DemandEnum;
use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\Bill;
use app\common\model\Demand;
use app\common\model\SettleOrder;
use think\facade\Db;
use app\api\logic\NoticeLogic;

/**
 * 账单逻辑层
 */
class BillLogic extends BaseLogic
{
    /**
     * 结算需求（创建结算支付订单）
     * @param int $userId 发布者用户ID
     * @param int $demandId 需求ID
     * @return array|false 返回 [order_id, from] 或 false
     */
    public static function settle(int $userId, int $demandId)
    {
        try {
            // 查询需求
            $demand = Demand::where('id', $demandId)->findOrEmpty();
            if ($demand->isEmpty()) {
                throw new \Exception('需求不存在');
            }

            // 验证是否是发布者
            if ($demand->user_id != $userId) {
                throw new \Exception('无权操作');
            }

            // 验证状态（只有已完成状态可结算）
            if ($demand->status != DemandEnum::STATUS_COMPLETED) {
                throw new \Exception('当前状态不允许结算');
            }

            // 检查是否已有已支付的结算订单
            $paidOrder = SettleOrder::where('demand_id', $demandId)
                ->where('pay_status', PayEnum::ISPAID)
                ->findOrEmpty();
            if (!$paidOrder->isEmpty()) {
                throw new \Exception('该需求已结算');
            }

            // 检查是否已有待支付订单（复用，避免重复创建）
            $pendingOrder = SettleOrder::where('demand_id', $demandId)
                ->where('pay_status', PayEnum::UNPAID)
                ->findOrEmpty();
            if (!$pendingOrder->isEmpty()) {
                // 重新计算金额，若价格有变动则更新订单
                $originalAmount = $demand->amount;
                $serviceRate = $demand->service_rate;
                $serviceFee = round($originalAmount * $serviceRate / 100, 2);
                $orderAmount = round($originalAmount + $serviceFee, 2);
                if ($pendingOrder->original_amount != $originalAmount || $pendingOrder->service_rate != $serviceRate) {
                    $pendingOrder->save([
                        'original_amount' => $originalAmount,
                        'service_fee' => $serviceFee,
                        'service_rate' => $serviceRate,
                        'order_amount' => $orderAmount,
                    ]);
                }
                return [
                    'order_id' => (int)$pendingOrder->id,
                    'from' => 'settle'
                ];
            }

            // 计算金额
            $originalAmount = $demand->amount;
            $serviceRate = $demand->service_rate;
            $serviceFee = round($originalAmount * $serviceRate / 100, 2);
            $orderAmount = round($originalAmount + $serviceFee, 2);

            // 创建结算订单
            $sn = generate_sn(SettleOrder::class, 'sn');
            $order = SettleOrder::create([
                'sn' => $sn,
                'user_id' => $userId,
                'demand_id' => $demandId,
                'pay_status' => PayEnum::UNPAID,
                'order_amount' => $orderAmount,
                'original_amount' => $originalAmount,
                'service_fee' => $serviceFee,
                'service_rate' => $serviceRate,
            ]);

            return [
                'order_id' => (int)$order->id,
                'from' => 'settle'
            ];
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 自动入账：将超过阈值的待入账账单改为已入账
     * @return int 更新数量
     */
    public static function autoSettle(): int
    {
        // TODO: 测试用1分钟，上线前改为7天 (7 * 24 * 3600)
        $threshold = 60;
        $count = Bill::where('status', BillEnum::STATUS_PENDING)
            ->where('create_time', '<=', time() - $threshold)
            ->update(['status' => BillEnum::STATUS_SETTLED]);
        return (int) $count;
    }

    /**
     * 提现申请
     * @param int $userId 用户ID
     * @param float $amount 提现金额
     * @return bool
     */
    public static function withdraw(int $userId, float $amount): bool
    {
        try {
            Db::startTrans();

            // 计算可用余额
            $income = Bill::where('user_id', $userId)
                ->where('type', BillEnum::TYPE_INCOME)
                ->where('status', BillEnum::STATUS_SETTLED)
                ->sum('amount');

            $expense = Bill::where('user_id', $userId)
                ->where('type', BillEnum::TYPE_EXPENSE)
                ->whereIn('status', [BillEnum::STATUS_PENDING, BillEnum::STATUS_SETTLED])
                ->sum('amount');

            $balance = round($income - $expense, 2);

            if ($amount > $balance) {
                throw new \Exception('余额不足');
            }

            if ($amount <= 0) {
                throw new \Exception('提现金额必须大于0');
            }

            // 生成账单编号
            $billNo = Bill::generateBillNo();

            // 创建支出记录
            Bill::create([
                'bill_no' => $billNo,
                'user_id' => $userId,
                'demand_id' => 0,
                'demand_no' => '',
                'type' => BillEnum::TYPE_EXPENSE,
                'amount' => $amount,
                'status' => BillEnum::STATUS_PENDING,
                'remark' => '提现申请',
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 账单列表（包含汇总）
     * @param int $userId 用户ID
     * @return array
     */
    public static function lists(int $userId): array
    {
        // 查询汇总数据
        $income = Bill::where('user_id', $userId)
            ->where('type', BillEnum::TYPE_INCOME)
            ->where('status', BillEnum::STATUS_SETTLED)
            ->sum('amount');

        $expense = Bill::where('user_id', $userId)
            ->where('type', BillEnum::TYPE_EXPENSE)
            ->whereIn('status', [BillEnum::STATUS_PENDING, BillEnum::STATUS_SETTLED])
            ->sum('amount');

        $balance = round($income - $expense, 2);

        // 查询账单列表（返回全部，前端过滤）
        $list = Bill::where('user_id', $userId)
            ->with(['demand' => function($q) {
                $q->field('id,title');
            }])
            ->order('create_time', 'desc')
            ->select()
            ->toArray();

        // 格式化列表数据
        $billList = [];
        foreach ($list as $item) {
            $title = $item['type'] == BillEnum::TYPE_EXPENSE
                ? '提现'
                : ($item['demand']['title'] ?? '需求结算');
            $billList[] = [
                'id' => $item['id'],
                'title' => $title,
                'time' => date('Y.m.d H:i', strtotime($item['create_time'])),
                'amount' => number_format($item['amount'], 2, '.', ''),
                'type' => $item['type'],
                'status' => $item['status'],
                'remark' => $item['remark'] ?? '',
            ];
        }

        return [
            'income' => number_format($income, 2, '.', ''),
            'expense' => number_format($expense, 2, '.', ''),
            'balance' => number_format($balance, 2, '.', ''),
            'list' => $billList,
        ];
    }
}
