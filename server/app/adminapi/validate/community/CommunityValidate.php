<?php

namespace app\adminapi\validate\community;

use app\common\validate\BaseValidate;
use app\common\model\Community;

/**
 * 小区管理验证
 * Class CommunityValidate
 * @package app\adminapi\validate\community
 */
class CommunityValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkCommunity',
        'name' => 'require|length:1,100',
        'province' => 'max:50',
        'city' => 'max:50',
        'district' => 'max:50',
        'address' => 'max:255',
        'status' => 'in:0,1,2',
    ];

    protected $message = [
        'id.require' => '小区ID不能为空',
        'name.require' => '小区名称不能为空',
        'name.length' => '小区名称长度须在1-100位字符',
        'status.in' => '审核状态不正确',
    ];

    /**
     * @notes 添加场景
     * @return CommunityValidate
     */
    public function sceneAdd()
    {
        return $this->remove(['id'])
            ->remove('id', 'require|checkCommunity');
    }

    /**
     * @notes 详情场景
     * @return CommunityValidate
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    /**
     * @notes 编辑场景
     * @return CommunityValidate
     */
    public function sceneEdit()
    {
        return $this;
    }

    /**
     * @notes 删除场景
     * @return CommunityValidate
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }

    /**
     * @notes 审核场景
     * @return CommunityValidate
     */
    public function sceneAudit()
    {
        return $this->only(['id', 'status']);
    }

    /**
     * @notes 检查指定小区是否存在
     * @param $value
     * @return bool|string
     */
    public function checkCommunity($value)
    {
        $community = Community::findOrEmpty($value);
        if ($community->isEmpty()) {
            return '小区不存在';
        }
        return true;
    }
}
