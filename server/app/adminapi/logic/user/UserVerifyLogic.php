<?php

namespace app\adminapi\logic\user;

use app\common\enum\UserVerifyEnum;
use app\common\logic\BaseLogic;
use app\common\model\user\UserVerify;

/**
 * 业主认证管理逻辑
 */
class UserVerifyLogic extends BaseLogic
{
    /**
     * @notes 审核认证
     * @param array $params
     * @return bool
     */
    public static function audit(array $params): bool
    {
        try {
            $verify = UserVerify::findOrEmpty($params['id']);
            if ($verify->isEmpty()) {
                throw new \Exception('认证记录不存在');
            }

            if ($verify['status'] != UserVerifyEnum::STATUS_PENDING) {
                throw new \Exception('该记录不是待审核状态');
            }

            $verify->status = $params['status'];
            if ($params['status'] == UserVerifyEnum::STATUS_REJECTED) {
                $verify->reject_reason = $params['reject_reason'] ?? '';
            }
            if ($params['status'] == UserVerifyEnum::STATUS_VERIFIED) {
                $verify->verified_at = date('Y-m-d H:i:s');
            }
            $verify->save();

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
