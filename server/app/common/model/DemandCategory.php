<?php

namespace app\common\model;

use app\common\service\FileService;
use think\model\concern\SoftDelete;

/**
 * 需求分类模型
 */
class DemandCategory extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

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
     * 获取器-图标
     */
    public function getIconAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * 设置器-图标
     */
    public function setIconAttr($value)
    {
        return trim($value) ? FileService::setFileUrl($value) : '';
    }
}
