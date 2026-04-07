<?php

namespace app\adminapi\controller\community;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\community\CommunityLists;
use app\adminapi\logic\community\CommunityLogic;
use app\adminapi\validate\community\CommunityValidate;

/**
 * 小区管理控制器
 * Class CommunityController
 * @package app\adminapi\controller\community
 */
class CommunityController extends BaseAdminController
{
    /**
     * @notes 小区总览统计
     * @return \think\response\Json
     */
    public function stat()
    {
        $result = CommunityLogic::stat();
        return $this->success('', $result);
    }

    /**
     * @notes 查看小区列表
     * @return \think\response\Json
     */
    public function lists()
    {
        return $this->dataLists(new CommunityLists());
    }

    /**
     * @notes 添加小区
     * @return \think\response\Json
     */
    public function add()
    {
        $params = (new CommunityValidate())->post()->goCheck('add');
        CommunityLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 编辑小区
     * @return \think\response\Json
     */
    public function edit()
    {
        $params = (new CommunityValidate())->post()->goCheck('edit');
        $result = CommunityLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(CommunityLogic::getError());
    }

    /**
     * @notes 删除小区
     * @return \think\response\Json
     */
    public function delete()
    {
        $params = (new CommunityValidate())->post()->goCheck('delete');
        CommunityLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 小区详情
     * @return \think\response\Json
     */
    public function detail()
    {
        $params = (new CommunityValidate())->goCheck('detail');
        $result = CommunityLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 审核小区
     * @return \think\response\Json
     */
    public function audit()
    {
        $params = (new CommunityValidate())->post()->goCheck('audit');
        $result = CommunityLogic::audit($params);
        if (true === $result) {
            return $this->success('审核成功', [], 1, 1);
        }
        return $this->fail(CommunityLogic::getError());
    }
}
