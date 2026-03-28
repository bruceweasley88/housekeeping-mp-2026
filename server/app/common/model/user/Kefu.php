<?php

namespace app\common\model\user;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 客服模型
 */
class Kefu extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected $name = 'kefu';

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
