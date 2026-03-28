<?php

namespace app\api\controller;

use app\api\logic\ChatLogic;

/**
 * 聊天控制器
 */
class ChatController extends BaseApiController
{
    public array $notNeedLogin = [];

    /**
     * 获取会话列表
     */
    public function sessionList()
    {
        $result = ChatLogic::getSessionList($this->userId);
        return $this->data(['list' => $result]);
    }

    /**
     * 创建/获取会话
     */
    public function sessionCreate()
    {
        $peerUserId = $this->request->post('user_id');
        $result = ChatLogic::createSession($this->userId, intval($peerUserId));
        if ($result === false) {
            return $this->fail(ChatLogic::getError());
        }
        return $this->success('success', $result);
    }

    /**
     * 发送消息
     */
    public function messageSend()
    {
        $sessionId = $this->request->post('session_id');
        $content = $this->request->post('content');
        $result = ChatLogic::sendMessage($this->userId, intval($sessionId), trim($content));
        if ($result === false) {
            return $this->fail(ChatLogic::getError());
        }
        return $this->success('success', $result);
    }

    /**
     * 获取消息列表
     */
    public function messageList()
    {
        $sessionId = $this->request->get('session_id');
        $lastId = $this->request->get('last_id', 0);
        $page = $this->request->get('page', 1);
        $pageSize = $this->request->get('page_size', 20);

        $result = ChatLogic::getMessageList(
            $this->userId,
            intval($sessionId),
            intval($lastId),
            intval($page),
            intval($pageSize)
        );

        if ($result === false) {
            return $this->fail(ChatLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * 标记消息已读
     */
    public function messageRead()
    {
        $sessionId = $this->request->post('session_id');
        $result = ChatLogic::markAsRead($this->userId, intval($sessionId));
        if ($result === false) {
            return $this->fail(ChatLogic::getError());
        }
        return $this->success('success');
    }

    /**
     * 获取客服用户ID
     */
    public function getKefu()
    {
        $result = ChatLogic::getKefu($this->userId);
        if ($result === false) {
            return $this->fail(ChatLogic::getError());
        }
        return $this->success('success', $result);
    }
}
