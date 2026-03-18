<?php

namespace app\api\controller;

use app\api\logic\DemandLogic;
use app\api\validate\DemandValidate;

/**
 * 需求控制器
 */
class DemandController extends BaseApiController
{
    public array $notNeedLogin = ['lists', 'detail'];

    /**
     * 需求列表（免登录）
     */
    public function lists()
    {
        $params = (new DemandValidate())->get()->goCheck('lists');
        $result = DemandLogic::lists($params);
        return $this->data($result);
    }

    /**
     * 需求详情（免登录，但有权限控制）
     */
    public function detail()
    {
        $params = (new DemandValidate())->get()->goCheck('detail');
        $result = DemandLogic::detail($params['id'], $this->userId);
        if (empty($result)) {
            return $this->fail(DemandLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * 发布需求（需登录）
     */
    public function publish()
    {
        $params = (new DemandValidate())->post()->goCheck('publish');
        $result = DemandLogic::publish($this->userId, $params);
        if ($result !== false) {
            return $this->success('发布成功', $result, 1, 1);
        }
        return $this->fail(DemandLogic::getError());
    }

    /**
     * 编辑需求（需登录）
     */
    public function edit()
    {
        $params = (new DemandValidate())->post()->goCheck('edit');
        $result = DemandLogic::edit($this->userId, $params);
        if ($result === true) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(DemandLogic::getError());
    }

    /**
     * 承接需求（需登录，需业主认证）
     */
    public function accept()
    {
        $params = (new DemandValidate())->post()->goCheck('accept');
        $result = DemandLogic::accept($this->userId, $params['demand_id']);
        if ($result === true) {
            return $this->success('承接成功', [], 1, 1);
        }
        return $this->fail(DemandLogic::getError());
    }

    /**
     * 取消承接（需登录）
     */
    public function cancelAccept()
    {
        $params = (new DemandValidate())->post()->goCheck('cancelAccept');
        $result = DemandLogic::cancelAccept($this->userId, $params['demand_id']);
        if ($result === true) {
            return $this->success('取消成功', [], 1, 1);
        }
        return $this->fail(DemandLogic::getError());
    }

    /**
     * 确认完成（需登录）
     */
    public function finish()
    {
        $params = (new DemandValidate())->post()->goCheck('finish');
        $result = DemandLogic::finish($this->userId, $params['demand_id']);
        if ($result === true) {
            return $this->success('确认完成', [], 1, 1);
        }
        return $this->fail(DemandLogic::getError());
    }

    /**
     * 调整金额（需登录）
     */
    public function adjustAmount()
    {
        $params = (new DemandValidate())->post()->goCheck('adjustAmount');
        $result = DemandLogic::adjustAmount($this->userId, $params['demand_id'], $params['amount']);
        if ($result === true) {
            return $this->success('调整成功', [], 1, 1);
        }
        return $this->fail(DemandLogic::getError());
    }

    /**
     * 我的需求列表（需登录）- 合并接口
     */
    public function myList()
    {
        $params = (new DemandValidate())->get()->goCheck('myList');
        $result = DemandLogic::myList($this->userId, $params);
        return $this->data($result);
    }

    /**
     * 删除需求（需登录）
     */
    public function delete()
    {
        $params = (new DemandValidate())->post()->goCheck('delete');
        $result = DemandLogic::delete($this->userId, $params['id']);
        if ($result === true) {
            return $this->success('删除成功', [], 1, 1);
        }
        return $this->fail(DemandLogic::getError());
    }
}
