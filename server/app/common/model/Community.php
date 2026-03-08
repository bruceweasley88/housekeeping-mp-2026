<?php

namespace app\common\model;

use app\common\enum\CommunityEnum;
use think\model\concern\SoftDelete;

/**
 * 小区模型
 * Class Community
 * @package app\common\model
 */
class Community extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    /**
     * @notes 搜索器-省市区
     * @param $query
     * @param $value
     * @param $data
     */
    public function searchProvinceAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('province', '=', $value);
        }
    }

    public function searchCityAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('city', '=', $value);
        }
    }

    public function searchDistrictAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('district', '=', $value);
        }
    }

    /**
     * @notes 搜索器-状态
     * @param $query
     * @param $value
     * @param $data
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value !== '' && $value !== null) {
            $query->where('status', '=', $value);
        }
    }

    /**
     * @notes 搜索器-关键词
     * @param $query
     * @param $value
     * @param $data
     */
    public function searchKeywordAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('name|address', 'like', '%' . $value . '%');
        }
    }

    /**
     * @notes 获取器-状态描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getStatusDescAttr($value, $data)
    {
        return CommunityEnum::getStatusDesc($data['status']);
    }
}
