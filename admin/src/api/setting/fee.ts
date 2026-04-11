import request from '@/utils/request'

// 获取手续费设置
export function getFeeSettings() {
    return request.get({ url: '/setting.fee_settings/getConfig' })
}

// 设置手续费
export function setFeeSettings(params: any) {
    return request.post({ url: '/setting.fee_settings/setConfig', params })
}
