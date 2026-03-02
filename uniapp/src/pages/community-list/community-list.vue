<template>
    <view class="page">
        <!-- 附近小区标题 -->
        <view class="location-header">
            <image class="location-icon" src="/static/images/community/icon_location.png" mode="aspectFit"/>
            <text class="location-text">附近小区</text>
        </view>

        <!-- 小区列表 -->
        <view class="community-list">
            <view
                class="community-item"
                :class="{ 'selected': selectedIndex === index }"
                v-for="(item, index) in communityList"
                :key="index"
                @click="handleSelect(index)"
            >
                <view class="community-info">
                    <text class="community-name">{{ item.name }}</text>
                    <text class="community-address">地址：{{ item.address }}</text>
                </view>
                <view class="check-icon" v-if="selectedIndex === index">
                    <text class="check-mark">✓</text>
                </view>
            </view>
        </view>

        <!-- 确定按钮 -->
        <view class="confirm-btn" @click="handleConfirm">
            <text class="confirm-text">确定</text>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// 小区列表数据
const communityList = ref([
    {
        name: '新港东琶洲新村',
        address: '广东省广州市海珠区太和里左十一巷3号'
    },
    {
        name: '新港东水博苑',
        address: '广东省广州市海珠区阅江中路690号'
    },
    {
        name: '保利天悦',
        address: '广东省广州市海珠区琶洲村新马路18号'
    },
    {
        name: '琶洲丽舍',
        address: '广东省广州市海珠区新港东路2348号'
    }
])

// 当前选中索引
const selectedIndex = ref(0)

// 选择小区
const handleSelect = (index: number) => {
    selectedIndex.value = index
}

// 确认选择
const handleConfirm = () => {
    const selectedCommunity = communityList.value[selectedIndex.value]
    // 返回上一页并传递选中的小区
    uni.$emit('community-selected', selectedCommunity)
    uni.navigateBack()
}
</script>

<style lang="scss" scoped>
.page {
    min-height: 100vh;
    background: #fff;
    display: flex;
    flex-direction: column;
}

// 附近小区标题
.location-header {
    display: flex;
    align-items: center;
    padding: 30rpx 36rpx;

    .location-icon {
        width: 31rpx;
        height: 31rpx;
        margin-right: 16rpx;
    }

    .location-text {
        font-size: 29rpx;
        font-weight: 500;
        color: #222929;
    }
}

// 小区列表
.community-list {
    flex: 1;
    padding: 0 36rpx;
}

.community-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 32rpx 0;
    border-bottom: 2rpx solid #F5F5F5;

    &.selected {
        .community-name {
            color: #00B6B4;
        }
    }
}

.community-info {
    flex: 1;
}

.community-name {
    font-size: 33rpx;
    font-weight: 500;
    color: #222929;
    display: block;
    margin-bottom: 8rpx;
}

.community-address {
    font-size: 25rpx;
    color: #9CA6A6;
}

.check-icon {
    width: 44rpx;
    height: 44rpx;
    background: #00B6B4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.check-mark {
    font-size: 24rpx;
    color: #fff;
}

// 确定按钮
.confirm-btn {
    margin: 40rpx 30rpx calc(40rpx + env(safe-area-inset-bottom));
    height: 102rpx;
    background: #00B6B4;
    border-radius: 51rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.confirm-text {
    font-size: 36rpx;
    font-weight: 500;
    color: #fff;
}
</style>