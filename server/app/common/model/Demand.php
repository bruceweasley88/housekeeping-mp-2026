<?php

namespace app\common\model;

use app\common\enum\DemandEnum;
use app\common\enum\DemandPriceTypeEnum;
use app\common\model\user\User;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

/**
 * 需求模型
 */
class Demand extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    /**
     * JSON字段
     */
    protected $json = ['images'];

    /**
     * 关联发布者
     */
    public function publisher()
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->field('id,nickname,avatar,mobile');
    }

    /**
     * 关联承接者
     */
    public function acceptor()
    {
        return $this->hasOne(User::class, 'id', 'accept_user_id')
            ->field('id,nickname,avatar,mobile');
    }

    /**
     * 关联分类
     */
    public function category()
    {
        return $this->hasOne(DemandCategory::class, 'id', 'category_id')
            ->field('id,name,icon');
    }

    /**
     * 关联小区
     */
    public function community()
    {
        return $this->hasOne(Community::class, 'id', 'community_id')
            ->field('id,name,address');
    }

    /**
     * 搜索器-用户ID
     */
    public function searchUserIdAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('user_id', '=', $value);
        }
    }

    /**
     * 搜索器-承接用户ID
     */
    public function searchAcceptUserIdAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('accept_user_id', '=', $value);
        }
    }

    /**
     * 搜索器-分类ID
     */
    public function searchCategoryIdAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('category_id', '=', $value);
        }
    }

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
     * 搜索器-关键词
     */
    public function searchKeywordAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('title|description', 'like', '%' . $value . '%');
        }
    }

    /**
     * 搜索器-小区ID
     */
    public function searchCommunityIdAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('community_id', '=', $value);
        }
    }

    /**
     * 搜索器-紧急
     */
    public function searchIsUrgentAttr($query, $value, $data)
    {
        if ($value !== '' && $value !== null) {
            $query->where('is_urgent', '=', $value);
        }
    }

    /**
     * 获取器-状态描述
     */
    public function getStatusDescAttr($value, $data)
    {
        return DemandEnum::getStatusDesc($data['status']);
    }

    /**
     * 获取器-金额类型描述
     */
    public function getPriceTypeDescAttr($value, $data)
    {
        return DemandPriceTypeEnum::getTypeDesc($data['price_type']);
    }

    /**
     * 获取器-图片（自动补全URL）
     */
    public function getImagesAttr($value)
    {
        if (empty($value)) {
            return [];
        }
        // 如果是对象则转换为数组
        if (is_object($value)) {
            $value = (array)$value;
        }
        // 确保是数组
        if (!is_array($value)) {
            return [];
        }
        return array_map(function ($item) {
            return FileService::getFileUrl($item);
        }, $value);
    }

    /**
     * 设置器-图片（去除域名）
     */
    public function setImagesAttr($value)
    {
        if (empty($value) || !is_array($value)) {
            return [];
        }
        return array_map(function ($item) {
            return FileService::setFileUrl($item);
        }, $value);
    }

    /**
     * 生成需求编号
     * 格式：DQ + 年月日 + 4位序号（如 DQ20260310001）
     */
    public static function generateDemandNo(): string
    {
        $prefix = 'DQ' . date('Ymd');

        // 查询当天最大的编号
        $lastDemand = self::where('demand_no', 'like', $prefix . '%')
            ->order('id', 'desc')
            ->find();

        if ($lastDemand && $lastDemand->demand_no) {
            $lastNo = (int)substr($lastDemand->demand_no, -4);
            $newNo = $lastNo + 1;
        } else {
            $newNo = 1;
        }

        return $prefix . str_pad((string)$newNo, 4, '0', STR_PAD_LEFT);
    }
}
