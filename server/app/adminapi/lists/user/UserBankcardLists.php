<?php

namespace app\adminapi\lists\user;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\lists\ListsSortInterface;
use app\common\model\user\UserBankcard;

/**
 * 银行卡认证列表
 */
class UserBankcardLists extends BaseAdminDataLists implements ListsSearchInterface, ListsSortInterface
{
    /**
     * @notes 设置搜索条件
     */
    public function setSearch(): array
    {
        return [
            '=' => ['ub.status'],
            '%like%' => ['u.nickname', 'u.mobile'],
            'between_time' => 'ub.create_time',
        ];
    }

    /**
     * @notes 设置支持排序字段
     */
    public function setSortFields(): array
    {
        return ['ub.create_time' => 'ub.create_time', 'ub.id' => 'ub.id'];
    }

    /**
     * @notes 设置默认排序
     */
    public function setDefaultOrder(): array
    {
        return ['ub.id' => 'desc'];
    }

    /**
     * @notes 获取列表
     */
    public function lists(): array
    {
        $field = 'ub.id,ub.user_id,u.nickname,u.mobile,ub.real_name,ub.bankcard_image,ub.status,ub.reject_reason,ub.create_time';
        return UserBankcard::alias('ub')
            ->join('user u', 'u.id = ub.user_id')
            ->field($field)
            ->where($this->searchWhere)
            ->append(['status_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order($this->sortOrder)
            ->select()
            ->toArray();
    }

    /**
     * @notes 获取数量
     */
    public function count(): int
    {
        return UserBankcard::alias('ub')
            ->join('user u', 'u.id = ub.user_id')
            ->where($this->searchWhere)
            ->count();
    }

    public function extend()
    {
        return [];
    }
}
