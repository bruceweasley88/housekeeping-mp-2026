<?php

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\model\DemandCategory;

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
        $list = DemandCategory::where('status', 1)
            ->field('id,name,icon,sort')
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();

        return $list;
    }
}
