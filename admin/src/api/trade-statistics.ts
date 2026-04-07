import request from '@/utils/request'

// 交易统计汇总（趋势 + 类型明细 + 发布中）
export function getTradeSummary(params?: any) {
    return request.get({ url: '/trade_statistics.index/summary', params })
}

// 小区成交统计
export function getCommunityStats(params?: any) {
    return request.get({ url: '/trade_statistics.index/communityStats', params })
}
