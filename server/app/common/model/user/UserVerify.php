<?php

namespace app\common\model\user;

use app\common\enum\UserVerifyEnum;
use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

/**
 * 业主认证模型
 */
class UserVerify extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected $name = 'user_verify';

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
     * 搜索器-状态
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value !== '' && $value !== null) {
            $query->where('status', '=', $value);
        }
    }

    /**
     * 获取器-状态描述
     */
    public function getStatusDescAttr($value, $data)
    {
        return UserVerifyEnum::getStatusDesc($data['status']);
    }

    /**
     * 获取器-身份证正面
     */
    public function getIdcardFrontAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * 获取器-身份证反面
     */
    public function getIdcardBackAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * 设置器-身份证正面
     */
    public function setIdcardFrontAttr($value)
    {
        return trim($value) ? FileService::setFileUrl($value) : '';
    }

    /**
     * 设置器-身份证反面
     */
    public function setIdcardBackAttr($value)
    {
        return trim($value) ? FileService::setFileUrl($value) : '';
    }

    /**
     * 获取器-证明材料（JSON转数组并补全URL）
     */
    public function getVerifyMaterialsAttr($value, $data)
    {
        if (empty($value)) {
            return [];
        }
        $materials = json_decode($value, true) ?: [];
        return array_map(function ($item) {
            return FileService::getFileUrl($item);
        }, $materials);
    }

    /**
     * 设置器-证明材料（数组转JSON并去除域名）
     */
    public function setVerifyMaterialsAttr($value)
    {
        if (empty($value) || !is_array($value)) {
            return '';
        }
        $materials = array_map(function ($item) {
            return FileService::setFileUrl($item);
        }, $value);
        return json_encode($materials, JSON_UNESCAPED_UNICODE);
    }
}
