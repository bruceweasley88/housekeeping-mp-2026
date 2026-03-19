import request from '@/utils/request'

// 提交银行卡
export function submitUserBankcard(data: {
    real_name: string
    bankcard_image: string
}) {
    return request.post({ url: '/user_bankcard/submit', data }, { isAuth: true })
}

// 获取银行卡详情
export function getUserBankcardDetail() {
    return request.get({ url: '/user_bankcard/detail' }, { isAuth: true })
}
