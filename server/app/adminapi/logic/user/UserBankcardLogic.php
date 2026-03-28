<?php

namespace app\adminapi\logic\user;

use app\common\enum\UserBankcardEnum;
use app\common\logic\BaseLogic;
use app\common\model\user\UserBankcard;

/**
 * 银行卡认证管理逻辑
 */
class UserBankcardLogic extends BaseLogic
{
    /**
     * @notes 审核认证
     * @param array $params
     * @return bool
     */
    public static function audit(array $params): bool
    {
        try {
            $bankcard = UserBankcard::findOrEmpty($params['id']);
            if ($bankcard->isEmpty()) {
                throw new \Exception('认证记录不存在');
            }

            if ($bankcard['status'] != UserBankcardEnum::STATUS_PENDING) {
                throw new \Exception('该记录不是待审核状态');
            }

            $bankcard->status = $params['status'];
            if ($params['status'] == UserBankcardEnum::STATUS_REJECTED) {
                $bankcard->reject_reason = $params['reject_reason'] ?? '';
            }
            if ($params['status'] == UserBankcardEnum::STATUS_VERIFIED) {
                $bankcard->verified_at = time();
            }
            $bankcard->save();

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
