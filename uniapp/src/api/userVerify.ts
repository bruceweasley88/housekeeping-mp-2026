import request from '@/utils/request'

// 提交业主认证
export function submitUserVerify(data: {
    idcard_front: string
    idcard_back: string
    verify_materials: string[]
}) {
    return request.post({ url: '/user_verify/submit', data }, { isAuth: true })
}

// 获取认证详情
export function getUserVerifyDetail() {
    return request.get({ url: '/user_verify/detail' }, { isAuth: true })
}
