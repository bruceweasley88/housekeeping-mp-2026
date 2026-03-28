<?php

namespace app\adminapi\controller\user;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\user\KefuLists;
use app\adminapi\logic\user\KefuLogic;
use app\adminapi\validate\user\KefuValidate;

/**
 * 客服管理控制器
 */
class KefuController extends BaseAdminController
{
    /**
     * 客服列表
     */
    public function lists()
    {
        return $this->dataLists(new KefuLists());
    }

    /**
     * 添加客服
     */
    public function add()
    {
        $params = (new KefuValidate())->post()->goCheck('add');
        $result = KefuLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(KefuLogic::getError());
    }

    /**
     * 移除客服
     */
    public function remove()
    {
        $params = (new KefuValidate())->post()->goCheck('remove');
        $result = KefuLogic::remove($params);
        if (true === $result) {
            return $this->success('移除成功', [], 1, 1);
        }
        return $this->fail(KefuLogic::getError());
    }

    /**
     * 更新客服状态
     */
    public function status()
    {
        $params = (new KefuValidate())->post()->goCheck('status');
        $result = KefuLogic::status($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(KefuLogic::getError());
    }
}
