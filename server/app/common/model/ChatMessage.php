<?php

namespace app\common\model;

/**
 * 聊天消息模型
 */
class ChatMessage extends BaseModel
{
    /**
     * 关联会话
     */
    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }

    /**
     * 搜索器：按会话ID
     */
    public function searchSessionIdAttr($query, $value)
    {
        $query->where('session_id', $value);
    }

    /**
     * 搜索器：按接收者ID
     */
    public function searchReceiverIdAttr($query, $value)
    {
        $query->where('receiver_id', $value);
    }

    /**
     * 搜索器：按已读状态
     */
    public function searchIsReadAttr($query, $value)
    {
        $query->where('is_read', $value);
    }
}
