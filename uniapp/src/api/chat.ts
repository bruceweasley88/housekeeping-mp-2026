import request from '@/utils/request'

// 获取会话列表
export function getChatSessionList() {
    return request.get({ url: '/chat/sessionList' }, { isAuth: true })
}

// 创建/获取会话
export function createChatSession(userId: number) {
    return request.post({ url: '/chat/sessionCreate', data: { user_id: userId } }, { isAuth: true })
}

// 发送消息
export function sendChatMessage(sessionId: number, content: string) {
    return request.post({ url: '/chat/messageSend', data: { session_id: sessionId, content } }, { isAuth: true })
}

// 获取消息列表
export function getChatMessageList(params: {
    session_id: number
    last_id?: number
    page?: number
    page_size?: number
}) {
    return request.get({ url: '/chat/messageList', data: params }, { isAuth: true })
}

// 标记消息已读
export function markChatMessageRead(sessionId: number) {
    return request.post({ url: '/chat/messageRead', data: { session_id: sessionId } }, { isAuth: true })
}

// 获取客服用户ID
export function getKefuUser() {
    return request.get({ url: '/chat/getKefu' }, { isAuth: true })
}
