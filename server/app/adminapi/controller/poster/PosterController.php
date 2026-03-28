<?php

namespace app\adminapi\controller\poster;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\poster\PosterLists;
use app\adminapi\logic\poster\PosterLogic;
use app\adminapi\validate\poster\PosterValidate;

/**
 * 海报管理控制器
 * Class PosterController
 * @package app\adminapi\controller\poster
 */
class PosterController extends BaseAdminController
{
    /**
     * @notes 查看海报列表
     */
    public function lists()
    {
        return $this->dataLists(new PosterLists());
    }

    /**
     * @notes 添加海报
     */
    public function add()
    {
        $params = (new PosterValidate())->post()->goCheck('add');
        PosterLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 编辑海报
     */
    public function edit()
    {
        $params = (new PosterValidate())->post()->goCheck('edit');
        $result = PosterLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(PosterLogic::getError());
    }

    /**
     * @notes 删除海报
     */
    public function delete()
    {
        $params = (new PosterValidate())->post()->goCheck('delete');
        PosterLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }
}
