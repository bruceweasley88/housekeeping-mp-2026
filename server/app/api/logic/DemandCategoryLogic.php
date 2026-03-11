<?php

namespace app\api\logic;

use app\common\logic\BaseLogic;
use think\facade\Db;

/**
 * 需求分类逻辑层
 */
class DemandCategoryLogic extends BaseLogic
{
    /**
     * 获取分类列表（免登录）
     */
    public static function lists(): array
    {
        // 使用 Db 直接查询，绕过模型的 icon 获取器，返回原始值
        $list = Db::name('demand_category')
            ->where('status', 1)
            ->field('id,name,icon,sort')
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();

        return $list;
    }
}
