<?php

namespace app\adminapi\lists\community;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\CommunityEnum;
use app\common\lists\ListsSearchInterface;
use app\common\lists\ListsSortInterface;
use app\common\model\Community;

/**
 * 小区列表
 * Class CommunityLists
 * @package app\adminapi\lists\community
 */
class CommunityLists extends BaseAdminDataLists implements ListsSearchInterface, ListsSortInterface
{
    /**
     * @notes 设置搜索条件
     * @return array
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['name', 'address'],
            '=' => ['province', 'city', 'district', 'status'],
        ];
    }

    /**
     * @notes 设置支持排序字段
     * @return array
     */
    public function setSortFields(): array
    {
        return ['create_time' => 'create_time', 'id' => 'id'];
    }

    /**
     * @notes 设置默认排序
     * @return array
     */
    public function setDefaultOrder(): array
    {
        return ['id' => 'desc'];
    }

    /**
     * @notes 获取管理列表
     * @return array
     */
    public function lists(): array
    {
        $list = Community::where($this->searchWhere)
            ->append(['status_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order($this->sortOrder)
            ->select()
            ->toArray();

        return $list;
    }

    /**
     * @notes 获取数量
     * @return int
     */
    public function count(): int
    {
        return Community::where($this->searchWhere)->count();
    }

    public function extend()
    {
        return [];
    }
}
