import request from '@/utils/request'

// 银行卡认证列表
export function getUserBankcardLists(params: any) {
    return request.get({ url: '/user.userBankcard/lists', params }, { ignoreCancelToken: true })
}

// 审核银行卡认证
export function userBankcardAudit(params: any) {
    return request.post({ url: '/user.userBankcard/audit', params })
}
