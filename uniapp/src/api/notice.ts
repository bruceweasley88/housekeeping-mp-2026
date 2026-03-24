import request from '@/utils/request'

// 获取公告列表
export function getNoticeLists() {
    return request.get({ url: '/notice/lists' }, { isAuth: true })
}

// 获取公告详情
export function getNoticeDetail(id: number) {
    return request.get({ url: '/notice/detail', data: { id } }, { isAuth: true })
}

// 获取未读数量
export function getNoticeUnreadCount() {
    return request.get({ url: '/notice/unreadCount' }, { isAuth: true })
}
