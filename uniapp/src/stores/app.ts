import { defineStore } from 'pinia'
import { getConfig } from '@/api/app'

interface AppSate {
    config: Record<string, any>
}
export const useAppStore = defineStore({
    id: 'appStore',
    state: (): AppSate => ({
        config: {
            domain: ''
        }
    }),
    getters: {
        getWebsiteConfig: (state) => state.config.website || {},
        getLoginConfig: (state) => state.config.login || {},
        getTabbarConfig: (state) => state.config.tabbar || [],
        getStyleConfig: (state) => state.config.style || {},
        getH5Config: (state) => state.config.webPage || {},
        getCopyrightConfig: (state) => state.config.copyright || [],
        getFeeConfig: (state) => state.config.fee || { urgent_fee_rate: 3, withdraw_fee_rate: 3 },
    },
    actions: {
        getImageUrl(url: string) {
            // 如果已经是完整URL，直接返回
            if (url.indexOf('http') === 0) {
                return url
            }

            // 获取域名并处理末尾斜杠
            let domain = this.config.domain || ''
            if (domain.endsWith('/')) {
                domain = domain.slice(0, -1)
            }

            // 处理URL开头的斜杠
            let path = url || ''
            if (path.startsWith('/')) {
                path = path.slice(1)
            }

            return `${domain}/${path}`
        },
        async getConfig() {
            const data = await getConfig()
            this.config = data
        }
    }
})
