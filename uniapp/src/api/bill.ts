import request from '@/utils/request'

// 获取账单列表（包含汇总）
export function getBillLists() {
    return request.get({ url: '/bill/lists' }, { isAuth: true })
}

// 自动入账（无权限）
export function autoSettleBill() {
    return request.get({ url: '/bill/autoSettle' })
}

// 结算需求
export function settleDemand(demand_id: number) {
    return request.post({ url: '/bill/settle', data: { demand_id } }, { isAuth: true })
}

// 提现申请
export function withdrawBill(amount: number) {
    return request.post({ url: '/bill/withdraw', data: { amount } }, { isAuth: true })
}
