<template>
    <view class="page">
        <view class="success-content">
            <view class="success-icon">
                <text class="icon-check">✓</text>
            </view>
            <text class="success-title">发布成功</text>
            <text class="success-desc">您的需求已发布成功，等待邻居承接</text>

        </view>

        <view class="bottom-area">
            <view class="btn-primary" @click="handleViewDetail">
                <text class="btn-primary-text">查看详情</text>
            </view>
            <view class="btn-secondary" @click="handleBackHome">
                <text class="btn-secondary-text">返回首页</text>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const demandId = ref(0)

// 查看详情
const handleViewDetail = () => {
    if (demandId.value) {
        uni.redirectTo({
            url: `/pages/demand/demand?id=${demandId.value}`
        })
    } else {
        uni.navigateBack()
    }
}

// 返回首页
const handleBackHome = () => {
    uni.switchTab({
        url: '/pages/index/index'
    })
}

onMounted(() => {
    // 获取页面参数
    const pages = getCurrentPages()
    const currentPage = pages[pages.length - 1] as any
    if (currentPage && currentPage.options) {
        demandId.value = currentPage.options.id ? parseInt(currentPage.options.id) : 0
    }
})
</script>

<style lang="scss" scoped>
.page {
    min-height: 100vh;
    background: #fff;
    display: flex;
    flex-direction: column;
}

.success-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 80rpx 36rpx;
}

.success-icon {
    width: 120rpx;
    height: 120rpx;
    background: #00B6B4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;

    .icon-check {
        font-size: 60rpx;
        color: #fff;
        font-weight: bold;
    }
}

.success-title {
    font-size: 40rpx;
    font-weight: 500;
    color: #222929;
    margin-top: 36rpx;
}

.success-desc {
    font-size: 29rpx;
    color: #616B6B;
    margin-top: 18rpx;
}

.bottom-area {
    padding: 30rpx 36rpx calc(30rpx + env(safe-area-inset-bottom));
    background: #fff;
}

.btn-primary {
    width: 100%;
    height: 98rpx;
    background: #00B6B4;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-primary-text {
    font-size: 32rpx;
    font-weight: 500;
    color: #fff;
}

.btn-secondary {
    width: 100%;
    height: 98rpx;
    margin-top: 22rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-secondary-text {
    font-size: 29rpx;
    color: #616B6B;
}
</style>
