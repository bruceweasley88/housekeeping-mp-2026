<template>
    <view class="page">
        <!-- 成功图标 -->
        <view class="success-icon">
            <image class="icon-img" src="/static/images/publish/icon_successb.png" mode="aspectFit" />
        </view>

        <!-- 成功文字 -->
        <text class="success-text">发布成功</text>

        <!-- 按钮区域 -->
        <view class="button-area">
            <view class="btn-primary" @click="handleViewDemand">
                <text class="btn-primary-text">查看需求</text>
            </view>
            <view class="btn-secondary" @click="handleBackHome">
                <text class="btn-secondary-text">返回首页</text>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
// 查看需求
const handleViewDemand = () => {
    // TODO: 跳转到需求详情页
    uni.navigateTo({
        url: '/pages/index/index'
    })
}

// 返回首页
const handleBackHome = () => {
    uni.switchTab({
        url: '/pages/index/index'
    })
}
</script>

<style lang="scss" scoped>
.page {
    min-height: 100vh;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 167rpx;
}

// 成功图标
.success-icon {
    width: 127rpx;
    height: 127rpx;
    display: flex;
    align-items: center;
    justify-content: center;

    .icon-img {
        width: 100%;
        height: 100%;
    }
}

// 成功文字
.success-text {
    font-size: 40rpx;
    font-weight: 500;
    color: #222929;
    margin-top: 18rpx;
}

// 按钮区域
.button-area {
    margin-top: 124rpx;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 22rpx;
}

.btn-primary {
    width: 418rpx;
    height: 98rpx;
    background: #00B6B4;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-primary-text {
    font-size: 33rpx;
    font-weight: 500;
    color: #fff;
}

.btn-secondary {
    width: 418rpx;
    height: 98rpx;
    background: #fff;
    border: 2rpx solid #00B6B4;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-secondary-text {
    font-size: 33rpx;
    font-weight: 500;
    color: #00A2A0;
}
</style>