<?php

namespace app\adminapi\lists\user;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\lists\ListsSortInterface;
use app\common\model\user\Kefu;

/**
 * 客服列表
 */
class KefuLists extends BaseAdminDataLists implements ListsSearchInterface, ListsSortInterface
{
    /**
     * 设置搜索条件
     */
    public function setSearch(): array
    {
        return [
            '=' => ['k.status'],
            '%like%' => ['u.nickname', 'u.mobile'],
        ];
    }

    /**
     * 设置支持排序字段
     */
    public function setSortFields(): array
    {
        return ['k.id' => 'k.id', 'k.create_time' => 'k.create_time'];
    }

    /**
     * 设置默认排序
     */
    public function setDefaultOrder(): array
    {
        return ['k.id' => 'desc'];
    }

    /**
     * 获取列表
     */
    public function lists(): array
    {
        $field = 'k.id,k.user_id,u.nickname,u.avatar,u.mobile,k.status,k.create_time';
        return Kefu::alias('k')
            ->join('user u', 'u.id = k.user_id')
            ->field($field)
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order($this->sortOrder)
            ->select()
            ->toArray();
    }

    /**
     * 获取数量
     */
    public function count(): int
    {
        return Kefu::alias('k')
            ->join('user u', 'u.id = k.user_id')
            ->where($this->searchWhere)
            ->count();
    }

    public function extend()
    {
        return [];
    }
}
