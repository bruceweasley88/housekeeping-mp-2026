<?php

namespace app\common\model;

/**
 * 聊天会话模型
 */
class ChatSession extends BaseModel
{
    /**
     * 关联消息
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'session_id');
    }

    /**
     * 搜索器：按用户1 ID
     */
    public function searchUser1IdAttr($query, $value)
    {
        $query->where('user1_id', $value);
    }

    /**
     * 搜索器：按用户2 ID
     */
    public function searchUser2IdAttr($query, $value)
    {
        $query->where('user2_id', $value);
    }
}
