<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\common\logic;

use app\common\enum\PayEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\BillEnum;
use app\common\enum\DemandEnum;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\User;
use app\common\model\Bill;
use app\common\model\Demand;
use app\common\model\SettleOrder;
use app\common\service\ConfigService;
use app\api\logic\NoticeLogic;
use think\facade\Db;
use think\facade\Log;

/**
 * 支付成功后处理订单状态
 * Class PayNotifyLogic
 * @package app\api\logic
 */
class PayNotifyLogic extends BaseLogic
{

    public static function handle($action, $orderSn, $extra = [])
    {
        Db::startTrans();
        try {
            self::$action($orderSn, $extra);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            Log::write(implode('-', [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ]));
            self::setError($e->getMessage());
            return $e->getMessage();
        }
    }


    /**
     * @notes 充值回调
     * @param $orderSn
     * @param array $extra
     * @author 段誉
     * @date 2023/2/27 15:28
     */
    public static function recharge($orderSn, array $extra = [])
    {
        $order = RechargeOrder::where('sn', $orderSn)->findOrEmpty();
        // 增加用户累计充值金额及用户余额
        $user = User::findOrEmpty($order->user_id);
        $user->total_recharge_amount += $order->order_amount;
        $user->user_money += $order->order_amount;
        $user->save();

        // 记录账户流水
        AccountLogLogic::add(
            $order->user_id,
            AccountLogEnum::UM_INC_RECHARGE,
            AccountLogEnum::INC,
            $order->order_amount,
            $order->sn,
            '用户充值'
        );

        // 更新充值订单状态
        $order->transaction_id = $extra['transaction_id'] ?? '';
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->save();
    }


    /**
     * @notes 需求结算支付回调
     * @param $orderSn
     * @param array $extra
     */
    public static function settle($orderSn, array $extra = [])
    {
        $order = SettleOrder::where('sn', $orderSn)->findOrEmpty();
        if ($order->isEmpty()) {
            throw new \Exception('结算订单不存在');
        }

        // 查询需求
        $demand = Demand::where('id', $order->demand_id)->findOrEmpty();
        if ($demand->isEmpty()) {
            throw new \Exception('需求不存在');
        }

        // 幂等检查：是否已有账单（防止重复回调）
        $existBill = Bill::where('demand_id', $order->demand_id)->findOrEmpty();
        if (!$existBill->isEmpty()) {
            return;
        }

        // 计算扣除平台佣金后的入账金额
        $commissionRate = ConfigService::get('fee', 'commission_rate', 0);
        $settleAmount = round($order->original_amount * (1 - $commissionRate / 100), 2);

        // 创建账单记录（给承接者入账，待入账状态）
        Bill::create([
            'bill_no' => Bill::generateBillNo(),
            'user_id' => $demand->accept_user_id,
            'demand_id' => $demand->id,
            'demand_no' => $demand->demand_no,
            'type' => BillEnum::TYPE_INCOME,
            'amount' => $settleAmount,
            'status' => BillEnum::STATUS_PENDING,
            'remark' => '需求结算收入',
            'settle_time' => time(),
        ]);

        // 更新需求状态为已结算
        $demand->save(['status' => DemandEnum::STATUS_SETTLED]);

        // 更新结算订单状态为已支付
        $order->transaction_id = $extra['transaction_id'] ?? '';
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->save();

        // 发送系统公告：通知承接者
        NoticeLogic::addNotice(
            $demand->accept_user_id,
            '需求已结算到账',
            '您承接的需求已完成结算，金额 ¥' . number_format($settleAmount, 2) . ' 已入账，可在我的账单查看详情。',
            'demand',
            $demand->id
        );
    }


}
