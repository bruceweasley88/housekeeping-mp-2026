<?php

namespace app\api\validate;

use app\common\validate\BaseValidate;

/**
 * 需求验证器
 */
class DemandValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
        'demand_id' => 'require|integer',  // 用于承接/取消/完成等接口
        'category_id' => 'integer',
        'title' => 'require|max:100',
        'description' => 'require',
        'images' => 'array',
        'price_type' => 'require|in:1,2,3',
        'amount' => 'requireIf:price_type,2|float|gt:0',
        'hours' => 'requireIf:price_type,1|float|gt:0',
        'hour_price' => 'requireIf:price_type,1|float|gt:0',
        'min_amount' => 'requireIf:price_type,3|float|gt:0',
        'max_amount' => 'requireIf:price_type,3|float|gt:0',
        'community_id' => 'integer',
        'address' => 'require|max:255',
        'contact_name' => 'require|max:50',
        'contact_phone' => 'require|mobile',
        'show_phone' => 'in:0,1',
        'is_urgent' => 'in:0,1',
        'status' => 'integer',
        'keyword' => 'max:100',
        'page_no' => 'integer|gt:0',
        'page_size' => 'integer|between:1,100',
        'type' => 'in:publish,accept',
    ];

    protected $message = [
        'id.require' => '需求ID不能为空',
        'id.integer' => '需求ID必须是整数',
        'demand_id.require' => '需求ID不能为空',
        'demand_id.integer' => '需求ID必须是整数',
        'category_id.integer' => '分类ID必须是整数',
        'title.require' => '请填写需求标题',
        'title.max' => '需求标题不能超过100个字符',
        'description.require' => '请填写需求描述',
        'images.array' => '图片格式错误',
        'price_type.require' => '请选择金额类型',
        'price_type.in' => '金额类型错误',
        'amount.requireIf' => '请填写金额',
        'amount.float' => '金额格式错误',
        'amount.gt' => '金额必须大于0',
        'hours.requireIf' => '请填写总小时数',
        'hours.float' => '小时数格式错误',
        'hours.gt' => '小时数必须大于0',
        'hour_price.requireIf' => '请填写每小时金额',
        'hour_price.float' => '每小时金额格式错误',
        'hour_price.gt' => '每小时金额必须大于0',
        'min_amount.requireIf' => '请填写最小金额',
        'min_amount.float' => '最小金额格式错误',
        'min_amount.gt' => '最小金额必须大于0',
        'max_amount.requireIf' => '请填写最大金额',
        'max_amount.float' => '最大金额格式错误',
        'max_amount.gt' => '最大金额必须大于0',
        'community_id.integer' => '小区ID必须是整数',
        'address.require' => '请填写详细地址',
        'address.max' => '地址不能超过255个字符',
        'contact_name.require' => '请填写联系人',
        'contact_name.max' => '联系人不能超过50个字符',
        'contact_phone.require' => '请填写联系电话',
        'contact_phone.mobile' => '手机号格式错误',
        'show_phone.in' => '是否展示手机号参数错误',
        'is_urgent.in' => '是否紧急发布参数错误',
        'status.integer' => '状态必须是整数',
        'keyword.max' => '关键词不能超过100个字符',
        'page_no.integer' => '页码必须是整数',
        'page_no.gt' => '页码必须大于0',
        'page_size.integer' => '每页数量必须是整数',
        'page_size.between' => '每页数量必须在1-100之间',
        'type.in' => '类型参数错误，只能是 publish 或 accept',
    ];

    /**
     * 发布需求场景
     */
    public function scenePublish()
    {
        return $this->only([
            'category_id', 'title', 'description', 'images',
            'price_type', 'amount', 'hours', 'hour_price', 'min_amount', 'max_amount',
            'community_id', 'address', 'contact_name', 'contact_phone', 'show_phone',
            'is_urgent'
        ])->append('category_id', 'require')->append('community_id', 'require');
    }

    /**
     * 编辑需求场景
     */
    public function sceneEdit()
    {
        return $this->only([
            'id', 'category_id', 'title', 'description', 'images',
            'price_type', 'amount', 'hours', 'hour_price', 'min_amount', 'max_amount',
            'community_id', 'address', 'contact_name', 'contact_phone', 'show_phone'
        ])->append('category_id', 'require')->append('community_id', 'require');
    }

    /**
     * 需求详情场景
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    /**
     * 承接需求场景
     */
    public function sceneAccept()
    {
        return $this->only(['demand_id']);
    }

    /**
     * 取消承接场景
     */
    public function sceneCancelAccept()
    {
        return $this->only(['demand_id']);
    }

    /**
     * 确认完成场景
     */
    public function sceneFinish()
    {
        return $this->only(['demand_id']);
    }

    /**
     * 调整金额场景
     */
    public function sceneAdjustAmount()
    {
        return $this->only(['demand_id', 'amount']);
    }

    /**
     * 列表场景
     */
    public function sceneLists()
    {
        return $this->only(['category_id', 'community_id', 'status', 'keyword', 'page_no', 'page_size']);
    }

    /**
     * 我的需求列表场景（合并接口）
     */
    public function sceneMyList()
    {
        return $this->only(['type', 'status', 'page_no', 'page_size']);
    }
}
