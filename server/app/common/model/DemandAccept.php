<?php

namespace app\common\model;

use app\common\model\user\User;

/**
 * 需求承接记录模型
 */
class DemandAccept extends BaseModel
{
    // 状态常量
    const STATUS_ACCEPTING = 1;  // 承接中
    const STATUS_CANCELLED = 2;  // 已取消
    const STATUS_COMPLETED = 3;  // 已完成

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
     * 搜索器-需求ID
     */
    public function searchDemandIdAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('demand_id', '=', $value);
        }
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
     * 搜索器-状态
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value !== '' && $value !== null) {
            $query->where('status', '=', $value);
        }
    }

    /**
     * 获取器-状态描述
     */
    public function getStatusDescAttr($value, $data)
    {
        $statusMap = [
            self::STATUS_ACCEPTING => '承接中',
            self::STATUS_CANCELLED => '已取消',
            self::STATUS_COMPLETED => '已完成',
        ];
        return $statusMap[$data['status']] ?? '';
    }
}
