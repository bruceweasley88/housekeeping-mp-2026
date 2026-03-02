<template>
    <view class="mutual-aid-list-page">
        <!-- 统计卡片区域 -->
        <view class="stats-section">
            <view class="stat-card">
                <view class="stat-info">
                    <view class="stat-number">
                        <text class="number">{{ publishCount }}</text>
                        <text class="unit">次</text>
                    </view>
                    <text class="stat-label">发布需求</text>
                </view>
                <image class="stat-icon" src="/static/images/icon/icon_fabuxuqiu.png" mode="aspectFit" />
            </view>
            <view class="stat-card">
                <view class="stat-info">
                    <view class="stat-number">
                        <text class="number">{{ receiveCount }}</text>
                        <text class="unit">次</text>
                    </view>
                    <text class="stat-label">承接需求</text>
                </view>
                <image class="stat-icon" src="/static/images/icon/icon_chengjiexuqiu.png" mode="aspectFit" />
            </view>
        </view>

        <!-- 类型切换 -->
        <view class="type-tabs">
            <view class="tab-item" :class="{ active: currentType === 'publish' }" @click="currentType = 'publish'">
                <text class="tab-text">我发布的</text>
                <view v-if="currentType === 'publish'" class="tab-indicator"></view>
            </view>
            <view class="tab-item" :class="{ active: currentType === 'receive' }" @click="currentType = 'receive'">
                <text class="tab-text">我承接的</text>
                <view v-if="currentType === 'receive'" class="tab-indicator"></view>
            </view>
        </view>

        <!-- 状态筛选 -->
        <view class="status-filter">
            <view
                v-for="(item, index) in statusList"
                :key="index"
                class="filter-item"
                :class="{ active: currentStatus === index }"
                @click="currentStatus = index"
            >
                <text class="filter-text">{{ item }}</text>
            </view>
        </view>

        <!-- 需求列表 -->
        <view class="demand-list">
            <demand-card
                v-for="(item, index) in demandList"
                :key="index"
                :tag="item.tag"
                :title="item.title"
                :location="item.location"
                :description="item.description"
                :price="item.price"
                :priceUnit="item.priceUnit"
                :image="item.image"
                :avatar="item.avatar"
                :username="item.username"
                :publishTime="item.publishTime"
                :actionText="item.actionText"
                @action="handleAction(item)"
                @location="handleLocation(item)"
            />
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// 统计数据
const publishCount = ref(26)
const receiveCount = ref(0)

// 类型切换
const currentType = ref<'publish' | 'receive'>('publish')

// 状态筛选
const statusList = ['全部需求', '发布中', '已完成', '已下架']
const currentStatus = ref(0)

// 需求列表数据
const demandList = ref([
    {
        tag: '紧急',
        title: '小三数学家教2小时',
        location: '保利天悦A10',
        description: '需要找小学三年级数学家教，周六日两天晚上19:00-21:00上课，合适的可长期…',
        price: '300',
        priceUnit: '元/小时',
        image: 'https://lanhu-oss-2537-2.lanhuapp.com/FigmaDDSSlicePNG3070b1e672a78d98edd43f2937a16db8.png',
        avatar: '/static/index_page/icon_avatar_default.png',
        username: '小伟妈妈',
        publishTime: '发布于2026.01.02 12:00',
        actionText: '查看详情',
        status: '发布中'
    },
    {
        tag: '紧急',
        title: '小三数学家教2小时',
        location: '保利天悦A10',
        description: '需要找小学三年级数学家教，周六日两天晚上19:00-21:00上课，合适的可长期…',
        price: '300',
        priceUnit: '元/小时',
        image: 'https://lanhu-oss-2537-2.lanhuapp.com/FigmaDDSSlicePNG3070b1e672a78d98edd43f2937a16db8.png',
        avatar: '/static/index_page/icon_avatar_default.png',
        username: '小伟妈妈',
        publishTime: '发布于2026.01.02 12:00',
        actionText: '已完成',
        status: '已完成'
    },
    {
        tag: '紧急',
        title: '小三数学家教2小时',
        location: '保利天悦A10',
        description: '需要找小学三年级数学家教，周六日两天晚上19:00-21:00上课，合适的可长期…',
        price: '300',
        priceUnit: '元/小时',
        image: 'https://lanhu-oss-2537-2.lanhuapp.com/FigmaDDSSlicePNG3070b1e672a78d98edd43f2937a16db8.png',
        avatar: '/static/index_page/icon_avatar_default.png',
        username: '小伟妈妈',
        publishTime: '发布于2026.01.02 12:00',
        actionText: '已下架',
        status: '已下架'
    }
])

// 操作按钮点击
const handleAction = (item: any) => {
    uni.showToast({
        title: `点击了: ${item.title}`,
        icon: 'none'
    })
}

// 地址点击
const handleLocation = (item: any) => {
    uni.showToast({
        title: `位置: ${item.location}`,
        icon: 'none'
    })
}
</script>

<style lang="scss" scoped>
.mutual-aid-list-page {
    background-color: #F6F6F6;
    min-height: 100vh;
    padding: 20rpx 30rpx;
}

// 统计卡片区域
.stats-section {
    display: flex;
    justify-content: space-between;
    margin-bottom: 29rpx;
}

.stat-card {
    background: #FFFFFF;
    border-radius: 29rpx;
    width: 338rpx;
    height: 153rpx;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20rpx;
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-number {
    display: flex;
    align-items: flex-end;

    .number {
        font-size: 47rpx;
        font-weight: 500;
        color: #000000;
    }

    .unit {
        font-size: 25rpx;
        font-weight: 500;
        color: #222222;
        margin-left: 5rpx;
        margin-bottom: 8rpx;
    }
}

.stat-label {
    font-size: 29rpx;
    color: #222222;
    margin-top: 11rpx;
}

.stat-icon {
    width: 82rpx;
    height: 82rpx;
}

// 类型切换
.type-tabs {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 29rpx;
}

.tab-item {
    position: relative;
    padding: 0 60rpx;

    &.active {
        .tab-text {
            color: #222222;
            font-weight: 500;
        }
    }
}

.tab-text {
    font-size: 33rpx;
    color: #777777;
}

.tab-indicator {
    position: absolute;
    bottom: -9rpx;
    left: 50%;
    transform: translateX(-50%);
    width: 131rpx;
    height: 9rpx;
    background: #00D4D1;
    border-radius: 5rpx;
}

// 状态筛选
.status-filter {
    display: flex;
    margin-bottom: 22rpx;
}

.filter-item {
    background: #FFFFFF;
    border-radius: 11rpx;
    padding: 11rpx 25rpx;
    margin-right: 18rpx;

    &.active {
        background: #00B6B4;

        .filter-text {
            color: #FFFFFF;
        }
    }
}

.filter-text {
    font-size: 27rpx;
    color: #444444;
}

// 需求列表
.demand-list {
    // 列表样式
}
</style>