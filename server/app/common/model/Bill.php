<?php

namespace app\common\model;

use app\common\enum\BillEnum;
use app\common\model\user\User;
use think\model\concern\SoftDelete;

/**
 * 账单模型
 */
class Bill extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->field('id,nickname,avatar,mobile');
    }

    /**
     * 关联需求
     */
    public function demand()
    {
        return $this->hasOne(Demand::class, 'id', 'demand_id');
    }

    /**
     * 搜索器-用户ID
     */
    public function searchUserIdAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('user_id', '=', $value);
        }
    }

    /**
     * 搜索器-需求ID
     */
    public function searchDemandIdAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('demand_id', '=', $value);
        }
    }

    /**
     * 搜索器-类型
     */
    public function searchTypeAttr($query, $value, $data)
    {
        if ($value !== '' && $value !== null) {
            $query->where('type', '=', $value);
        }
    }

    /**
     * 搜索器-状态
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value !== '' && $value !== null) {
            $query->where('status', '=', $value);
        }
    }

    /**
     * 获取器-类型描述
     */
    public function getTypeDescAttr($value, $data)
    {
        return BillEnum::getTypeDesc($data['type']);
    }

    /**
     * 获取器-状态描述
     */
    public function getStatusDescAttr($value, $data)
    {
        return BillEnum::getStatusDesc($data['status']);
    }

    /**
     * 生成账单编号
     * 格式：BN + 年月日 + 4位序号（如 BN20260320001）
     */
    public static function generateBillNo(): string
    {
        $prefix = 'BN' . date('Ymd');

        // 查询当天最大的编号
        $lastBill = self::where('bill_no', 'like', $prefix . '%')
            ->order('id', 'desc')
            ->find();

        if ($lastBill && $lastBill->bill_no) {
            $lastNo = (int)substr($lastBill->bill_no, -4);
            $newNo = $lastNo + 1;
        } else {
            $newNo = 1;
        }

        return $prefix . str_pad((string)$newNo, 4, '0', STR_PAD_LEFT);
    }
}
