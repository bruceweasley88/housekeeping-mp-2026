import request from '@/utils/request'

// 需求分类列表
export function getDemandCategoryLists() {
    return request.get({ url: '/demand_category/lists' })
}

// 需求列表
export function getDemandLists(params?: {
    category_id?: number
    community_id?: number
    status?: number
    keyword?: string
    page_no?: number
    page_size?: number
}) {
    return request.get({ url: '/demand/lists', data: params })
}

// 需求详情
export function getDemandDetail(id: number) {
    return request.get({ url: '/demand/detail', data: { id } })
}

// 发布需求
export function publishDemand(data: {
    category_id: number
    title: string
    description: string
    images?: string[]
    price_type: number  // 1=按小时，2=按次，3=按范围
    hours?: number
    hour_price?: number
    amount?: number
    min_amount?: number
    max_amount?: number
    community_id: number
    address: string
    contact_name: string
    contact_phone: string
    show_phone?: number
    is_urgent?: number
}) {
    return request.post({ url: '/demand/publish', data }, { isAuth: true })
}

// 编辑需求
export function editDemand(data: {
    id: number
    category_id: number
    title: string
    description: string
    images?: string[]
    price_type: number
    hours?: number
    hour_price?: number
    amount?: number
    min_amount?: number
    max_amount?: number
    community_id: number
    address: string
    contact_name: string
    contact_phone: string
    show_phone?: number
}) {
    return request.post({ url: '/demand/edit', data }, { isAuth: true })
}

// 承接需求
export function acceptDemand(demand_id: number) {
    return request.post({ url: '/demand/accept', data: { demand_id } }, { isAuth: true })
}

// 取消承接
export function cancelAcceptDemand(demand_id: number) {
    return request.post({ url: '/demand/cancelAccept', data: { demand_id } }, { isAuth: true })
}

// 确认完成
export function finishDemand(demand_id: number) {
    return request.post({ url: '/demand/finish', data: { demand_id } }, { isAuth: true })
}

// 调整金额
export function adjustDemandAmount(demand_id: number, amount: number) {
    return request.post({ url: '/demand/adjustAmount', data: { demand_id, amount } }, { isAuth: true })
}

// 我发布的需求
export function getMyPublishDemands(params?: {
    status?: number
    page_no?: number
    page_size?: number
}) {
    return request.get({ url: '/demand/myPublish', data: params }, { isAuth: true })
}

// 我承接的需求
export function getMyAcceptDemands(params?: {
    status?: number
    page_no?: number
    page_size?: number
}) {
    return request.get({ url: '/demand/myAccept', data: params }, { isAuth: true })
}
