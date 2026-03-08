<?php

namespace app\api\validate;

use app\common\validate\BaseValidate;

/**
 * 小区验证器
 * Class CommunityValidate
 * @package app\api\validate
 */
class CommunityValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'province' => 'require',
        'city' => 'require',
        'district' => 'require',
        'community_id' => 'require|integer',
        'building' => 'require',
        'room' => 'require',
    ];

    protected $message = [
        'name.require' => '请填写小区名称',
        'province.require' => '请选择省份',
        'city.require' => '请选择城市',
        'district.require' => '请选择区县',
        'community_id.require' => '请选择小区',
        'community_id.integer' => '小区ID必须是整数',
        'building.require' => '请填写楼号',
        'room.require' => '请填写门牌号',
    ];

    /**
     * @notes 申请添加小区场景
     * @return CommunityValidate
     */
    public function sceneApply()
    {
        return $this->only(['name', 'province', 'city', 'district']);
    }

    /**
     * @notes 保存地址场景
     * @return CommunityValidate
     */
    public function sceneSaveAddress()
    {
        return $this->only(['community_id', 'building', 'room']);
    }
}
