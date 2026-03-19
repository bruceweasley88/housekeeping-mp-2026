<template>
    <view class="index">
        <!-- 顶部地址栏 -->
        <view class="address-bar" @click="handleSelectCommunity">
            <image class="address-icon" src="/static/index_page/icon_addressb.png" mode="aspectFit" />
            <text class="address-text">{{ addressText || '请选择小区' }}</text>
            <text class="address-arrow">></text>
        </view>

        <!-- 轮播海报 -->
        <view class="banner-wrapper" v-if="bannerContent?.data?.length && bannerContent?.enabled">
            <l-swiper :content="bannerContent" height="200" :circular="true" :effect3d="false" borderRadius="29"
                interval="7000" bgColor="transparent" />
        </view>

        <!-- 需求流程 -->
        <view class="flow-section">
            <view class="flow-content">
                <view class="flow-header">
                    <view class="flow-label-placeholder"></view>
                    <view class="flow-steps">
                        <text class="step">发布需求</text>
                        <text class="step-arrow">›</text>
                        <text class="step">承接需求</text>
                        <text class="step-arrow">›</text>
                        <text class="step">完成需求</text>
                        <text class="step-arrow">›</text>
                        <text class="step">平台结算</text>
                    </view>
                </view>
                <view class="publish-btn" @click="handlePublish">发布需求</view>
            </view>
        </view>

        <!-- 分类网格 -->
        <view class="category-section">
            <view class="category-grid">
                <view class="category-item" v-for="item in categoryList" :key="item.id" @click="handleCategoryClick(item)">
                    <image class="category-icon" :src="getCategoryIcon(item.icon)" mode="aspectFit" />
                    <text class="category-name">{{ item.name }}</text>
                </view>
            </view>
        </view>

        <!-- 最新需求 -->
        <view class="demand-section">
            <text class="section-title">最新需求</text>

            <!-- 未选择小区提示 -->
            <view v-if="!communityId" class="empty-tip select-tip" @click="handleSelectCommunity">
                <text>请选择小区查看相关内容</text>
            </view>

            <!-- 空状态 -->
            <view v-else-if="latestDemandList.length === 0" class="empty-tip">
                <text>暂无需求</text>
            </view>

            <!-- 需求列表 -->
            <demand-card
                v-for="item in latestDemandList"
                v-else
                :key="item.id"
                :is-urgent="item.is_urgent"
                :title="item.title"
                :location="item.address"
                :description="item.description"
                :price-type="item.price_type"
                :amount="item.amount"
                :hour-price="item.hour_price"
                :min-amount="item.min_amount"
                :max-amount="item.max_amount"
                :image="item.images?.[0] || ''"
                :avatar="item.user_info?.avatar || ''"
                :username="item.user_info?.nickname || ''"
                :create-time="item.create_time"
                action-text="查看详情"
                @card-click="handleTakeTask(item)"
                @action="handleTakeTask(item)"
            />
        </view>

        <!--  #ifdef MP  -->
        <!--  微信小程序隐私弹窗  -->
        <MpPrivacyPopup></MpPrivacyPopup>
        <!--  #endif  -->

        <!-- 登录弹窗 -->
        <login-popup v-model:show="showLogin" @confirm="handleLoginConfirm" @cancel="handleLoginCancel" />

        <!-- 测试登录按钮（仅开发调试） -->
        <view class="test-login-btn" @click="handleTestLogin">测试登录</view>

        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { getIndex } from '@/api/shop'
import { getUserAddress } from '@/api/community'
import { getDemandCategoryLists, getDemandLists } from '@/api/demand'
import { login } from '@/api/account'
import { onLoad, onShow, onReady } from "@dcloudio/uni-app";
import { computed, reactive, ref, watch, onMounted, onUnmounted } from 'vue'
import LSwiper from '@/components/l-swiper/l-swiper.vue'
import { useUserStore } from '@/stores/user'

// #ifdef MP
import MpPrivacyPopup from './component/mp-privacy-popup.vue'
// #endif

import DemandCard from '@/components/demand-card/demand-card.vue'
import LoginPopup from '@/components/login-popup/login-popup.vue'

const userStore = useUserStore()

const state = reactive<{
    pages: any[]
    meta: any[]
}>({
    pages: [],
    meta: []
})

// 用户地址信息
const addressText = ref('')
const communityId = ref<number | null>(null)

// 需求分类列表
const categoryList = ref<{ id: number; name: string; icon: string }[]>([])

// 最新需求列表
const latestDemandList = ref<any[]>([])

// 获取分类列表（添加"全部"静态项）
const fetchCategoryList = async () => {
    try {
        const res = await getDemandCategoryLists()
        // 在前面添加"全部需求"静态项
        categoryList.value = [
            { id: 0, name: '全部需求', icon: 'icon_all.png' },
            ...(res || [])
        ]
    } catch (e) {
        // 使用默认分类
        categoryList.value = [
            { id: 0, name: '全部需求', icon: 'icon_all.png' }
        ]
    }
}

// 获取最新需求列表（最多15条）
const fetchLatestDemands = async () => {
    try {
        const params: any = { page_size: 15 }
        if (communityId.value) {
            params.community_id = communityId.value
        }
        const res = await getDemandLists(params)
        latestDemandList.value = res?.list || []
    } catch (e) {
        latestDemandList.value = []
    }
}

// 获取分类图标完整路径
const getCategoryIcon = (icon: string) => {
    return `/static/index_page/${icon}`
}

// 分类点击事件
const handleCategoryClick = (item: { id: number; name: string }) => {
    // 未选择小区时提示
    if (!communityId.value) {
        uni.showToast({
            title: '请选择小区',
            icon: 'none'
        })
        return
    }
    uni.navigateTo({
        url: `/pages/all-demands/all-demands?category_id=${item.id}`
    })
}


// 获取banner组件的数据
const bannerContent = computed(() => {
    const banner = state.pages.find((item: any) => item.name === 'banner')
    return banner?.content || {}
})

const getData = async () => {
    const data = await getIndex()
    state.pages = JSON.parse(data?.page?.data || '[]')
    state.meta = JSON.parse(data?.page?.meta || '[]')
}

// 获取用户地址
const fetchUserAddress = async () => {
    try {
        const res = await getUserAddress()
        if (res && res.community_name) {
            // 保存 community_id
            communityId.value = res.community_id

            // 显示小区名 + 楼号 + 门牌号
            const parts = [res.community_name]
            if (res.building) parts.push(res.building)
            if (res.room) parts.push(res.room)
            addressText.value = parts.join(' ')
        }
    } catch (e) {
        // 未登录或无地址信息
        addressText.value = ''
        communityId.value = null
    }
}


// 封装首页数据加载方法
const loadPageData = async () => {
    // 先并行加载不依赖地址的数据
    await Promise.all([
        getData(),
        fetchCategoryList(),
        fetchUserAddress()
    ])
    // 获取地址后再加载需求列表（需要 community_id）
    await fetchLatestDemands()
}

onLoad(() => {
    loadPageData()
})

// 每次显示页面时刷新数据
onShow(() => {
    loadPageData()
})

// 监听登录成功事件
onMounted(() => {
    uni.$on('loginSuccess', () => {
        loadPageData()
    })
})

// 组件销毁时移除监听
onUnmounted(() => {
    uni.$off('loginSuccess')
})

// 跳转选择小区页面
const handleSelectCommunity = () => {
    uni.navigateTo({
        url: '/pages/select-community/select-community'
    })
}

// 登录弹窗
const showLogin = ref(false)

// 监听 needShowLoginPopup 状态，控制登录弹窗显示
watch(
    () => userStore.needShowLoginPopup,
    (val) => {
        if (val) {
            showLogin.value = true
            userStore.setNeedShowLoginPopup(false) // 重置状态
        }
    },
    { immediate: true }
)

const handlePublish = () => {
    // 已登录但未选择小区时提示
    if (userStore.isLogin && !communityId.value) {
        uni.showToast({
            title: '请选择小区',
            icon: 'none'
        })
        return
    }
    uni.navigateTo({
        url: '/pages/publish-demand/publish-demand'
    })
}

const handleLoginConfirm = (data: { avatar: string; nickname: string }) => {
    console.log('登录确认:', data)
    uni.showToast({
        title: `欢迎 ${data.nickname}`,
        icon: 'none'
    })
}

const handleLoginCancel = () => {
    console.log('暂不登录')
}

// 测试账号登录（仅开发调试用）
const handleTestLogin = async () => {
    try {
        const data = await login({
            account: 'wx6688',
            password: 'wx123456',
            scene: 1
        })
        userStore.login(data.token)
        await loadPageData()
        uni.showToast({
            title: '测试账号登录成功',
            icon: 'none'
        })
    } catch (error: any) {
        uni.showToast({
            title: error || '登录失败',
            icon: 'none'
        })
    }
}

const handleTakeTask = (item: any) => {
    if (item?.id) {
        uni.navigateTo({
            url: `/pages/demand/demand?id=${item.id}`
        })
    }
}
</script>

<style lang="scss" scoped>
.index {
    min-height: 100vh;
    background: linear-gradient(90deg, #C3FBEF 0%, #C0F6FD 100%);
    padding-bottom: calc(120rpx + env(safe-area-inset-bottom));
}

// 顶部地址栏
.address-bar {
    display: flex;
    align-items: center;
    padding: 20rpx 30rpx;

    .address-icon {
        width: 32rpx;
        height: 32rpx;
        margin-right: 10rpx;
    }

    .address-text {
        font-size: 28rpx;
        color: #222929;
        font-weight: 500;
        max-width: 500rpx;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .address-arrow {
        font-size: 24rpx;
        color: #616B6B;
        margin-left: 8rpx;
    }
}

// 轮播海报
.banner-wrapper {
    margin: 0 29rpx;
    width: 692rpx;
    height: 200rpx;
    border-radius: 29rpx;
    overflow: hidden;
}

// 需求流程
.flow-section {
    margin: 20rpx 30rpx;
    height: 230rpx;
    border-radius: 29rpx;
    overflow: hidden;
    background:
        url('/static/index_page/icon_issuewzz@2x.png') no-repeat left center,
        linear-gradient(0deg, #FFFFFF 0%, #F3FAFA 74.04%, #ACF5F4 100%);
    background-size: auto 100%, 100% 100%;

    .flow-content {
        position: relative;
        z-index: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .flow-header {
        display: flex;
        align-items: center;
        padding: 0 30rpx;
        flex: 1;
    }

    .flow-steps {
        flex: 1;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8rpx;
        margin-top: -28rpx;
    }

    .flow-label-placeholder {
        width: 66rpx;
        margin-right: 20rpx;
        flex-shrink: 0;
        align-self: stretch;
    }

    .flow-steps {
        flex: 1;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8rpx;
    }

    .step {
        font-size: 26rpx;
        color: #222929;
    }

    .step-arrow {
        font-size: 44rpx;
        color: #DADADA;
        margin: 0 4rpx;
        transform: translateY(-2rpx);
    }

    .publish-btn {
        height: 80rpx;
        background: #00B6B4;
        border-radius: 40rpx;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 32rpx;
        font-weight: 500;
        margin: 0 30rpx;
        margin-bottom: 24rpx;
    }
}

// 分类网格
.category-section {
    margin: 20rpx 30rpx;
    background: #fff;
    border-radius: 24rpx;
    padding: 30rpx;

    .category-grid {
        display: flex;
        flex-wrap: wrap;
        position: relative;
        top: 10rpx;
    }

    .category-item {
        width: 25%;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 30rpx;
    }

    .category-icon {
        width: 55rpx;
        height: 55rpx;
        margin-bottom: 12rpx;
    }

    .category-name {
        font-size: 24rpx;
        color: #222929;
    }
}

// 最新需求
.demand-section {
    margin: 20rpx 30rpx;

    .section-title {
        font-size: 32rpx;
        font-weight: bold;
        color: #222929;
        margin-bottom: 20rpx;
        display: block;
    }

    .empty-tip {
        text-align: center;
        padding: 60rpx 0;
        color: #9CA6A6;
        font-size: 28rpx;

        &.select-tip {
            color: #00A2A0;
        }
    }
}

// 测试登录按钮
.test-login-btn {
    position: fixed;
    right: 20rpx;
    bottom: calc(200rpx + env(safe-area-inset-bottom));
    background: #FF7E22;
    color: #fff;
    font-size: 24rpx;
    padding: 16rpx 24rpx;
    border-radius: 30rpx;
    z-index: 999;
}
</style>
