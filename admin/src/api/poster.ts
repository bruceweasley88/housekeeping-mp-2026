import request from '@/utils/request'

// 海报列表
export function getPosterLists(params: any) {
    return request.get({ url: '/poster.poster/lists', params })
}

// 添加海报
export function posterAdd(params: any) {
    return request.post({ url: '/poster.poster/add', params })
}

// 编辑海报
export function posterEdit(params: any) {
    return request.post({ url: '/poster.poster/edit', params })
}

// 删除海报
export function posterDelete(params: any) {
    return request.post({ url: '/poster.poster/delete', params })
}
