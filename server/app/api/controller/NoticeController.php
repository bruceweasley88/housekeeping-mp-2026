<?php

namespace app\api\controller;

use app\api\logic\NoticeLogic;

/**
 * 用户公告控制器
 */
class NoticeController extends BaseApiController
{
    public array $notNeedLogin = [];

    /**
     * 公告列表
     */
    public function lists()
    {
        $result = NoticeLogic::lists($this->userId);
        return $this->data($result);
    }

    /**
     * 公告详情（同时标记已读）
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $result = NoticeLogic::detail($this->userId, intval($id));
        if ($result === false) {
            return $this->fail(NoticeLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * 未读数量
     */
    public function unreadCount()
    {
        $count = NoticeLogic::unreadCount($this->userId);
        return $this->data(['count' => $count]);
    }
}
