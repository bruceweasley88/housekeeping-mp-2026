<?php

namespace app\common\model;

use app\common\enum\PayEnum;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 需求结算订单模型
 * Class SettleOrder
 */
class SettleOrder extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    /**
     * 支付方式文字
     */
    public function getPayWayTextAttr($value, $data)
    {
        return PayEnum::getPayDesc($data['pay_way']);
    }

    /**
     * 支付状态文字
     */
    public function getPayStatusTextAttr($value, $data)
    {
        return PayEnum::getPayStatusDesc($data['pay_status']);
    }
}
