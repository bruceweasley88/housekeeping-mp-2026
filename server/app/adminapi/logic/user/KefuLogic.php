<?php

namespace app\adminapi\logic\user;

use app\common\logic\BaseLogic;
use app\common\model\user\Kefu;
use app\common\model\user\User;

/**
 * 客服管理逻辑
 */
class KefuLogic extends BaseLogic
{
    /**
     * 添加客服
     */
    public static function add(array $params): bool
    {
        try {
            $user = User::findOrEmpty($params['user_id']);
            if ($user->isEmpty()) {
                throw new \Exception('用户不存在');
            }

            $exists = Kefu::where('user_id', $params['user_id'])->findOrEmpty();
            if (!$exists->isEmpty()) {
                throw new \Exception('该用户已经是客服');
            }

            Kefu::create([
                'user_id' => $params['user_id'],
                'status'  => 1,
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 移除客服
     */
    public static function remove(array $params): bool
    {
        try {
            $kefu = Kefu::findOrEmpty($params['id']);
            if ($kefu->isEmpty()) {
                throw new \Exception('客服记录不存在');
            }
            $kefu->force(true)->delete();
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 更新客服状态
     */
    public static function status(array $params): bool
    {
        try {
            $kefu = Kefu::findOrEmpty($params['id']);
            if ($kefu->isEmpty()) {
                throw new \Exception('客服记录不存在');
            }
            $kefu->status = $params['status'];
            $kefu->save();
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
