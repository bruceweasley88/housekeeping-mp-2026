<?php

namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\FeeSettingsLogic;
use app\adminapi\validate\setting\FeeSettingsValidate;

/**
 * 手续费设置
 * Class FeeSettingsController
 * @package app\adminapi\controller\setting
 */
class FeeSettingsController extends BaseAdminController
{
    /**
     * @notes 获取手续费设置
     */
    public function getConfig()
    {
        $result = FeeSettingsLogic::getConfig();
        return $this->data($result);
    }

    /**
     * @notes 设置手续费
     */
    public function setConfig()
    {
        $params = (new FeeSettingsValidate())->post()->goCheck('setConfig');
        FeeSettingsLogic::setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}
