<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="index">
        <!-- 顶部地址栏 -->
        <view class="address-bar">
            <image class="address-icon" src="/static/index_page/icon_addressb@2x.png" mode="aspectFit"/>
            <text class="address-text">新港东琶洲新村</text>
            <text class="address-arrow">></text>
        </view>

        <!-- 轮播海报 -->
        <view class="banner-wrapper" v-if="bannerContent?.data?.length && bannerContent?.enabled">
            <l-swiper
                :content="bannerContent"
                height="200"
                :circular="true"
                :effect3d="false"
                borderRadius="29"
                interval="7000"
                bgColor="transparent"
            />
        </view>

        <!-- 需求流程 -->
        <view class="flow-section">
            <view class="flow-content">
                <view class="flow-header">
                    <view class="flow-label-placeholder"></view>
                    <view class="flow-steps">
                        <text class="step">发布需求</text>
                        <text class="step-arrow">></text>
                        <text class="step">承接需求</text>
                        <text class="step-arrow">></text>
                        <text class="step">完成需求</text>
                        <text class="step-arrow">></text>
                        <text class="step">平台结算</text>
                    </view>
                </view>
                <view class="publish-btn" @click="handlePublish">发布需求</view>
            </view>
        </view>

        <!-- 分类网格 -->
        <view class="category-section">
            <view class="category-grid">
                <view class="category-item">
                    <image class="category-icon" src="/static/index_page/icon_all@2x.png" mode="aspectFit"/>
                    <text class="category-name">全部需求</text>
                </view>
                <view class="category-item">
                    <image class="category-icon" src="/static/index_page/icon_soft@2x.png" mode="aspectFit"/>
                    <text class="category-name">轻需求</text>
                </view>
                <view class="category-item">
                    <image class="category-icon" src="/static/index_page/icon_car@2x.png" mode="aspectFit"/>
                    <text class="category-name">接送需求</text>
                </view>
                <view class="category-item">
                    <image class="category-icon" src="/static/index_page/icon_repair@2x.png" mode="aspectFit"/>
                    <text class="category-name">维修需求</text>
                </view>
                <view class="category-item">
                    <image class="category-icon" src="/static/index_page/icon_keep@2x.png" mode="aspectFit"/>
                    <text class="category-name">陪伴需求</text>
                </view>
                <view class="category-item">
                    <image class="category-icon" src="/static/index_page/icon_buy@2x.png" mode="aspectFit"/>
                    <text class="category-name">物品交易</text>
                </view>
                <view class="category-item">
                    <image class="category-icon" src="/static/index_page/icon_clean@2x.png" mode="aspectFit"/>
                    <text class="category-name">家政服务</text>
                </view>
                <view class="category-item">
                    <image class="category-icon" src="/static/index_page/icon_other@2x.png" mode="aspectFit"/>
                    <text class="category-name">其他需求</text>
                </view>
            </view>
        </view>

        <!-- 最新需求 -->
        <view class="demand-section">
            <text class="section-title">最新需求</text>
            <demand-card
                tag="紧急"
                title="小三数学家教2小时"
                location="保利天悦A10"
                description="需要找小学三年级数学家教，周六日两天晚上19:00-21:00上课，合适的可长期…"
                price="300"
                priceUnit="元/小时"
                image="/static/index_page/img_demand_1.png"
                username="小伟妈妈"
                publishTime="发布于2026.01.02 12:00"
                @action="handleTakeTask"
            />
        </view>

        <!--  #ifdef MP  -->
        <!--  微信小程序隐私弹窗  -->
        <MpPrivacyPopup></MpPrivacyPopup>
        <!--  #endif  -->

        <!-- 登录弹窗 -->
        <login-popup v-model:show="showLogin" @confirm="handleLoginConfirm" @cancel="handleLoginCancel"/>

        <tabbar/>
    </view>
</template>

<script setup lang="ts">
import {getIndex} from '@/api/shop'
import {onLoad} from "@dcloudio/uni-app";
import {computed, reactive, ref} from 'vue'
import LSwiper from '@/components/l-swiper/l-swiper.vue'

// #ifdef MP
import MpPrivacyPopup from './component/mp-privacy-popup.vue'
// #endif

import DemandCard from '@/components/demand-card/demand-card.vue'
import LoginPopup from '@/components/login-popup/login-popup.vue'

const state = reactive<{
    pages: any[]
    meta: any[]
}>({
    pages: [],
    meta: []
})


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


onLoad(() => { getData() })

// 登录弹窗
const showLogin = ref(false)

const handlePublish = () => {
    showLogin.value = true
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

const handleTakeTask = () => {
    uni.showToast({
        title: '接取任务',
        icon: 'none'
    })
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
        margin-top: -10rpx;
    }

    .flow-label-placeholder {
        width: 100rpx;
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
        font-size: 24rpx;
        color: #222929;
    }

    .step-arrow {
        font-size: 22rpx;
        color: #DADADA;
        margin: 0 4rpx;
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
}
</style>
