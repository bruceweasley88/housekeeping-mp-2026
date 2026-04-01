<?php

namespace app\adminapi\logic\finance;

use app\common\enum\BillEnum;
use app\common\logic\BaseLogic;
use app\common\model\Bill;

/**
 * 提现管理逻辑
 */
class BillLogic extends BaseLogic
{
    /**
     * 审核通过
     * @param int $id 账单ID
     * @return bool
     */
    public static function approve(int $id): bool
    {
        try {
            $bill = Bill::findOrEmpty($id);
            if ($bill->isEmpty()) {
                throw new \Exception('账单记录不存在');
            }

            if ($bill->status != BillEnum::STATUS_PENDING) {
                throw new \Exception('当前状态不允许审核');
            }

            $bill->save([
                'status' => BillEnum::STATUS_SETTLED,
                'settle_time' => time(),
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 审核拒绝
     * @param int $id 账单ID
     * @param string $remark 拒绝原因
     * @return bool
     */
    public static function reject(int $id, string $remark): bool
    {
        try {
            $bill = Bill::findOrEmpty($id);
            if ($bill->isEmpty()) {
                throw new \Exception('账单记录不存在');
            }

            if ($bill->status != BillEnum::STATUS_PENDING) {
                throw new \Exception('当前状态不允许审核');
            }

            $bill->save([
                'status' => BillEnum::STATUS_REJECTED,
                'remark' => $remark,
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
