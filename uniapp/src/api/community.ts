import request from '@/utils/request'

// 获取小区列表
export function getCommunityLists(params: {
    province?: string
    city?: string
    district?: string
    page?: number
    size?: number
}) {
    return request.get({ url: '/community/lists', data: params })
}

// 搜索小区
export function searchCommunity(params: {
    keyword?: string
    province?: string
    city?: string
    district?: string
    page?: number
    size?: number
}) {
    return request.get({ url: '/community/search', data: params })
}

// 申请添加小区
export function applyCommunity(data: {
    name: string
    province: string
    city: string
    district: string
    address?: string
}) {
    return request.post({ url: '/community/apply', data }, { isAuth: true })
}

// 获取用户地址信息
export function getUserAddress() {
    return request.get({ url: '/user/addressInfo' }, { isAuth: false })
}

// 保存用户地址
export function saveUserAddress(data: {
    community_id: number
    building: string
    room: string
}) {
    return request.post({ url: '/user/addressSave', data }, { isAuth: true })
}
