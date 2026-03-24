<?php

namespace app\api\logic;

use app\common\enum\BillEnum;
use app\common\enum\DemandEnum;
use app\common\logic\BaseLogic;
use app\common\model\Bill;
use app\common\model\Demand;
use think\facade\Db;
use app\api\logic\NoticeLogic;

/**
 * 账单逻辑层
 */
class BillLogic extends BaseLogic
{
    /**
     * 结算需求
     * @param int $userId 发布者用户ID
     * @param int $demandId 需求ID
     * @return bool
     */
    public static function settle(int $userId, int $demandId): bool
    {
        try {
            Db::startTrans();

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

            // 验证是否已结算（检查是否已有账单记录）
            $existBill = Bill::where('demand_id', $demandId)->findOrEmpty();
            if (!$existBill->isEmpty()) {
                throw new \Exception('该需求已结算');
            }

            // 计算金额
            $originalAmount = $demand->amount;           // 原始金额
            $serviceRate = $demand->service_rate;        // 服务费率
            $serviceFee = round($originalAmount * $serviceRate / 100, 2);  // 服务费
            $settleAmount = round($originalAmount - $serviceFee, 2);       // 结算金额

            // 生成账单编号
            $billNo = Bill::generateBillNo();

            // 创建账单记录
            Bill::create([
                'bill_no' => $billNo,
                'user_id' => $demand->accept_user_id,    // 承接者
                'demand_id' => $demandId,
                'demand_no' => $demand->demand_no,
                'type' => BillEnum::TYPE_INCOME,          // 收入
                'amount' => $settleAmount,                // 实际到账金额
                'status' => BillEnum::STATUS_PENDING,     // 待入账
                'remark' => '需求结算收入',
                'settle_time' => time(),
            ]);

            // 更新需求状态为已结算
            $demand->save([
                'status' => DemandEnum::STATUS_SETTLED,
            ]);

            // 发送系统公告：通知承接者
            NoticeLogic::addNotice(
                $demand->accept_user_id,
                '需求已结算到账',
                '您承接的需求已完成结算，金额 ¥' . number_format($settleAmount, 2) . ' 已入账，可在我的账单查看详情。',
                'demand',
                $demand->id
            );

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
            ->sum('amount');

        $expense = Bill::where('user_id', $userId)
            ->where('type', BillEnum::TYPE_EXPENSE)
            ->sum('amount');

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
            $billList[] = [
                'id' => $item['id'],
                'title' => $item['demand']['title'] ?? '需求结算',
                'time' => date('Y.m.d H:i', strtotime($item['create_time'])),
                'amount' => number_format($item['amount'], 2, '.', ''),
                'type' => $item['type'],
                'status' => $item['status'],
            ];
        }

        return [
            'income' => number_format($income, 2, '.', ''),
            'expense' => number_format($expense, 2, '.', ''),
            'list' => $billList,
        ];
    }
}
