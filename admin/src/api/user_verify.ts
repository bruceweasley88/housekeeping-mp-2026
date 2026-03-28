import request from '@/utils/request'

// 业主认证列表
export function getUserVerifyLists(params: any) {
    return request.get({ url: '/user.userVerify/lists', params }, { ignoreCancelToken: true })
}

// 审核业主认证
export function userVerifyAudit(params: any) {
    return request.post({ url: '/user.userVerify/audit', params })
}
