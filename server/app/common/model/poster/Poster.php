<?php

namespace app\common\model\poster;

use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

/**
 * 海报管理模型
 * Class Poster
 * @package app\common\model\poster
 */
class Poster extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected $name = 'poster';

    /**
     * @notes 获取图片完整URL
     */
    public function getImageAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * @notes 设置图片去除域名
     */
    public function setImageAttr($value)
    {
        return trim($value) ? FileService::setFileUrl($value) : '';
    }
}
