import request from '@/utils/request'
import { getUserList } from './consumer'

// 客服列表
export function getKefuLists(params: any) {
    return request.get({ url: '/user.kefu/lists', params }, { ignoreCancelToken: true })
}

// 添加客服
export function kefuAdd(params: any) {
    return request.post({ url: '/user.kefu/add', params })
}

// 移除客服
export function kefuRemove(params: any) {
    return request.post({ url: '/user.kefu/remove', params })
}

// 更新客服状态
export function kefuStatus(params: any) {
    return request.post({ url: '/user.kefu/status', params })
}

// 用户列表（添加客服弹窗复用）
export { getUserList }
