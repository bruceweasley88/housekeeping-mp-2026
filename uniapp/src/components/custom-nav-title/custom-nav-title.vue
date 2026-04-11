<template>
    <view>
        <view class="custom-nav-title" :style="{ paddingTop: statusBarHeight + 'px' }">
            <view class="nav-bar">
                <text class="nav-title">{{ title }}</text>
            </view>
        </view>
        <!-- 占位，防止内容被固定导航栏遮挡 -->
        <view :style="{ height: (statusBarHeight + 44) + 'px' }"></view>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
    title: string
}>()

const statusBarHeight = ref(0)
try {
    const systemInfo = uni.getSystemInfoSync()
    statusBarHeight.value = systemInfo.statusBarHeight || 0
} catch (e) {
    statusBarHeight.value = 0
}
</script>

<style lang="scss" scoped>
.custom-nav-title {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999;
    background: transparent;
}

.nav-bar {
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-title {
    font-size: 32rpx;
    font-weight: 500;
    color: #222929;
}
</style>
