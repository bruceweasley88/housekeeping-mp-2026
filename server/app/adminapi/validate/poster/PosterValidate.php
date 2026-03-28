<?php

namespace app\adminapi\validate\poster;

use app\common\validate\BaseValidate;
use app\common\model\poster\Poster;

/**
 * 海报管理验证
 * Class PosterValidate
 * @package app\adminapi\validate\poster
 */
class PosterValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkPoster',
        'name' => 'require|max:100',
        'image' => 'require',
        'sort' => 'number',
        'is_show' => 'in:0,1',
    ];

    protected $message = [
        'id.require' => '海报ID不能为空',
        'name.require' => '海报名称不能为空',
        'name.max' => '海报名称最多100个字符',
        'image.require' => '海报图片不能为空',
        'sort.number' => '排序必须为数字',
    ];

    /**
     * @notes 添加场景
     */
    public function sceneAdd()
    {
        return $this->remove(['id'])
            ->remove('id', 'require|checkPoster');
    }

    /**
     * @notes 编辑场景
     */
    public function sceneEdit()
    {
    }

    /**
     * @notes 删除场景
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }

    /**
     * @notes 检查海报是否存在
     */
    public function checkPoster($value)
    {
        $poster = Poster::findOrEmpty($value);
        if ($poster->isEmpty()) {
            return '海报不存在';
        }
        return true;
    }
}
