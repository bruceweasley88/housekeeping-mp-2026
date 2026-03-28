<?php

namespace app\adminapi\lists\poster;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\lists\ListsSortInterface;
use app\common\model\poster\Poster;

/**
 * 海报列表
 * Class PosterLists
 * @package app\adminapi\lists\poster
 */
class PosterLists extends BaseAdminDataLists implements ListsSearchInterface, ListsSortInterface
{
    /**
     * @notes 设置搜索条件
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['name'],
            '=' => ['is_show']
        ];
    }

    /**
     * @notes 设置支持排序字段
     */
    public function setSortFields(): array
    {
        return ['sort' => 'sort', 'id' => 'id', 'create_time' => 'create_time'];
    }

    /**
     * @notes 设置默认排序
     */
    public function setDefaultOrder(): array
    {
        return ['sort' => 'desc', 'id' => 'desc'];
    }

    /**
     * @notes 获取列表
     */
    public function lists(): array
    {
        return Poster::where($this->searchWhere)
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
        return Poster::where($this->searchWhere)->count();
    }

    public function extend()
    {
        return [];
    }
}
