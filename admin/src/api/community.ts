import request from '@/utils/request'

// 小区列表
export function getCommunityLists(params: any) {
    return request.get({ url: '/community.community/lists', params }, { ignoreCancelToken: true })
}

// 审核小区
export function communityAudit(params: any) {
    return request.post({ url: '/community.community/audit', params })
}
