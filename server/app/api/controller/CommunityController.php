<?php

namespace app\api\controller;

use app\api\logic\CommunityLogic;
use app\api\validate\CommunityValidate;

/**
 * 小区控制器
 * Class CommunityController
 * @package app\api\controller
 */
class CommunityController extends BaseApiController
{
    public array $notNeedLogin = ['lists', 'search'];

    /**
     * @notes 获取小区列表
     * @return \think\response\Json
     */
    public function lists()
    {
        $params = $this->request->get();
        $result = CommunityLogic::lists($params);
        return $this->data($result);
    }

    /**
     * @notes 搜索小区
     * @return \think\response\Json
     */
    public function search()
    {
        $params = $this->request->get();
        $result = CommunityLogic::search($params);
        return $this->data($result);
    }

    /**
     * @notes 申请添加小区
     * @return \think\response\Json
     */
    public function apply()
    {
        $params = (new CommunityValidate())->post()->goCheck('apply');
        $params['user_id'] = $this->userId;
        $result = CommunityLogic::apply($params);
        if ($result === false) {
            return $this->fail(CommunityLogic::getError());
        }
        return $this->success('申请成功，请等待审核', [], 1, 1);
    }
}
