<?php

namespace app\adminapi\lists\user;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\lists\ListsSortInterface;
use app\common\model\user\UserVerify;

/**
 * 业主认证列表
 */
class UserVerifyLists extends BaseAdminDataLists implements ListsSearchInterface, ListsSortInterface
{
    /**
     * @notes 设置搜索条件
     */
    public function setSearch(): array
    {
        return [
            '=' => ['uv.status'],
            '%like%' => ['u.nickname', 'u.mobile'],
            'between_time' => 'uv.create_time',
        ];
    }

    /**
     * @notes 设置支持排序字段
     */
    public function setSortFields(): array
    {
        return ['uv.create_time' => 'uv.create_time', 'uv.id' => 'uv.id'];
    }

    /**
     * @notes 设置默认排序
     */
    public function setDefaultOrder(): array
    {
        return ['uv.id' => 'desc'];
    }

    /**
     * @notes 获取列表
     */
    public function lists(): array
    {
        $field = 'uv.id,uv.user_id,u.nickname,u.mobile,u.avatar,uv.idcard_front,uv.idcard_back,uv.verify_materials,uv.status,uv.reject_reason,uv.create_time';
        $lists = UserVerify::alias('uv')
            ->join('user u', 'u.id = uv.user_id')
            ->field($field)
            ->where($this->searchWhere)
            ->append(['status_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order($this->sortOrder)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $materials = $item['verify_materials'] ?? [];
            $item['materials_count'] = is_array($materials) ? count($materials) : 0;
        }

        return $lists;
    }

    /**
     * @notes 获取数量
     */
    public function count(): int
    {
        return UserVerify::alias('uv')
            ->join('user u', 'u.id = uv.user_id')
            ->where($this->searchWhere)
            ->count();
    }

    public function extend()
    {
        return [];
    }
}
