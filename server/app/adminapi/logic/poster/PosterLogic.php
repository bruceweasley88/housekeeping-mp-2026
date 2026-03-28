<?php

namespace app\adminapi\logic\poster;

use app\common\logic\BaseLogic;
use app\common\model\poster\Poster;

/**
 * 海报管理逻辑
 * Class PosterLogic
 * @package app\adminapi\logic\poster
 */
class PosterLogic extends BaseLogic
{
    /**
     * @notes 添加海报
     */
    public static function add(array $params)
    {
        Poster::create([
            'name' => $params['name'],
            'image' => $params['image'],
            'sort' => $params['sort'] ?? 0,
            'is_show' => $params['is_show'] ?? 1,
        ]);
    }

    /**
     * @notes 编辑海报
     */
    public static function edit(array $params): bool
    {
        try {
            Poster::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'image' => $params['image'],
                'sort' => $params['sort'] ?? 0,
                'is_show' => $params['is_show'] ?? 1,
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 删除海报
     */
    public static function delete(array $params)
    {
        Poster::destroy($params['id']);
    }
}
