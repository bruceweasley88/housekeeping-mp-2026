<?php

namespace app\common\model;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 用户公告模型
 */
class UserNotice extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    /**
     * 搜索器：按用户ID
     */
    public function searchUserIdAttr($query, $value)
    {
        $query->where('user_id', $value);
    }

    /**
     * 搜索器：按已读状态
     */
    public function searchIsReadAttr($query, $value)
    {
        $query->where('is_read', $value);
    }
}
