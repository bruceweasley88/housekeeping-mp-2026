<template>
    <view class="about-us">
        <!-- Logo 区域 -->
        <view class="header">
            <image
                class="logo"
                src="/static/images/about-us/logo.png"
                mode="aspectFit"
            />
            <text class="slogan">社区邻居生活互助平台</text>
        </view>

        <!-- 功能入口列表 -->
        <view class="menu-card">
            <navigator url="/pages/agreement/agreement?type=trade">
                <view class="menu-item flex justify-between items-center">
                    <text class="menu-text">交易规则</text>
                    <image
                        class="arrow-icon"
                        src="/static/images/about-us/icon_arrow.png"
                        mode="aspectFit"
                    />
                </view>
            </navigator>
            <navigator url="/pages/agreement/agreement?type=service">
                <view class="menu-item flex justify-between items-center">
                    <text class="menu-text">用户协议</text>
                    <image
                        class="arrow-icon"
                        src="/static/images/about-us/icon_arrow.png"
                        mode="aspectFit"
                    />
                </view>
            </navigator>
            <navigator url="/pages/agreement/agreement?type=privacy">
                <view class="menu-item flex justify-between items-center border-bottom">
                    <text class="menu-text">隐私协议</text>
                    <image
                        class="arrow-icon"
                        src="/static/images/about-us/icon_arrow.png"
                        mode="aspectFit"
                    />
                </view>
            </navigator>
        </view>

        <!-- 退出登录按钮 -->
        <view class="logout-btn" @click="showLogout = true">
            <text class="logout-text">退出登录</text>
        </view>

        <!-- 退出确认弹窗 -->
        <u-popup
            v-model="showLogout"
            mode="center"
            border-radius="20"
            :mask-close-able="false"
        >
            <view class="popup-content">
                <text class="popup-title">温馨提示</text>
                <text class="popup-desc">是否清除当前登录信息，退出登录？</text>
                <view class="popup-btns flex">
                    <view class="btn btn-cancel" @click="showLogout = false">
                        <text>取消</text>
                    </view>
                    <view class="btn btn-confirm" @click="handleLogout">
                        <text>确认</text>
                    </view>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'
import { useRouter } from 'uniapp-router-next'

const router = useRouter()
const userStore = useUserStore()
const showLogout = ref(false)

// 退出登录
const handleLogout = () => {
    userStore.logout()
    router.switchTab('/pages/index/index')
}
</script>

<style lang="scss" scoped>
.about-us {
    min-height: 100vh;
    background-color: #f6f6f6;
    padding: 0 30rpx;
}

.header {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 260rpx;
    margin-bottom: 320rpx;
}

.logo {
    width: 226rpx;
    height: 69rpx;
}

.slogan {
    margin-top: 13rpx;
    font-size: 29rpx;
    color: #222222;
    letter-spacing: 8rpx;
}

.menu-card {
    background-color: #ffffff;
    border-radius: 29rpx;
    overflow: hidden;
}

.menu-item {
    padding: 29rpx;
}

.menu-item:not(:last-child) {
    border-bottom: 2rpx solid #f6f6f6;
}

.menu-text {
    font-size: 29rpx;
    color: #000000;
}

.arrow-icon {
    width: 16rpx;
    height: 26rpx;
}

.logout-btn {
    margin-top: 29rpx;
    background-color: #ffffff;
    border-radius: 51rpx;
    height: 102rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logout-text {
    font-size: 33rpx;
    color: #9ca6a6;
}

.popup-content {
    width: 560rpx;
    padding: 40rpx;
    background-color: #ffffff;
    border-radius: 20rpx;
}

.popup-title {
    display: block;
    font-size: 36rpx;
    font-weight: 500;
    text-align: center;
    color: #222222;
}

.popup-desc {
    display: block;
    padding: 30rpx 0 40rpx;
    font-size: 28rpx;
    color: #666666;
    text-align: center;
}

.popup-btns {
    gap: 20rpx;
}

.btn {
    flex: 1;
    height: 72rpx;
    border-radius: 36rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28rpx;
}

.btn-cancel {
    background-color: #ffffff;
    border: 2rpx solid #00b6b4;
    color: #00b6b4;
}

.btn-confirm {
    background-color: #00b6b4;
    color: #ffffff;
}
</style>
