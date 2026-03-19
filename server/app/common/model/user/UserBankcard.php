<?php

namespace app\common\model\user;

use app\common\enum\UserBankcardEnum;
use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

/**
 * 用户银行卡模型
 */
class UserBankcard extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected $name = 'user_bankcard';

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
        return UserBankcardEnum::getStatusDesc($data['status']);
    }

    /**
     * 获取器-银行卡图片
     */
    public function getBankcardImageAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * 设置器-银行卡图片
     */
    public function setBankcardImageAttr($value)
    {
        return trim($value) ? FileService::setFileUrl($value) : '';
    }
}
