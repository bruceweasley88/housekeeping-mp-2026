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
                        <text class="number">{{ acceptCount }}</text>
                        <text class="unit">次</text>
                    </view>
                    <text class="stat-label">承接需求</text>
                </view>
                <image class="stat-icon" src="/static/images/icon/icon_chengjiexuqiu.png" mode="aspectFit" />
            </view>
        </view>

        <!-- 类型切换 -->
        <view class="type-tabs">
            <view class="tab-item" :class="{ active: currentType === 'publish' }" @click="switchType('publish')">
                <text class="tab-text">我发布的</text>
                <view v-if="currentType === 'publish'" class="tab-indicator"></view>
            </view>
            <view class="tab-item" :class="{ active: currentType === 'accept' }" @click="switchType('accept')">
                <text class="tab-text">我承接的</text>
                <view v-if="currentType === 'accept'" class="tab-indicator"></view>
            </view>
        </view>

        <!-- 状态筛选 -->
        <view class="status-filter">
            <view
                v-for="(item, index) in currentStatusOptions"
                :key="index"
                class="filter-item"
                :class="{ active: currentStatusIndex === index }"
                @click="switchStatus(index)"
            >
                <text class="filter-text">{{ item.label }}</text>
            </view>
        </view>

        <!-- 需求列表 -->
        <view class="demand-list">
            <block v-if="!loading && demandList.length > 0">
                <demand-card
                    v-for="item in demandList"
                    :key="item.id"
                    :is-urgent="item.is_urgent"
                    :title="item.title"
                    :location="formatLocation(item)"
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
                    @card-click="handleAction(item)"
                    @action="handleAction(item)"
                />
            </block>

            <!-- 加载中 -->
            <view v-if="loading" class="loading-wrap">
                <text class="loading-text">加载中...</text>
            </view>

            <!-- 空状态 -->
            <view v-if="!loading && demandList.length === 0" class="empty-wrap">
                <text class="empty-text">暂无需求</text>
            </view>
        </view>

        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import { getMyDemandList } from '@/api/demand'

// 类型切换
const currentType = ref<'publish' | 'accept'>('publish')

// 状态筛选配置 - 根据 tab 动态变化
const publishStatusOptions = [
    { label: '全部需求', value: null },
    { label: '发布中', value: 'publishing' },   // [1, 2]
    { label: '已完成', value: 'completed' },     // [3, 6]
    { label: '已下架', value: 'offline' },       // [4, 5]
]

const acceptStatusOptions = [
    { label: '全部承接', value: null },
    { label: '进行中', value: 'ongoing' },       // [2]
    { label: '已完成', value: 'completed' },     // [3]
    { label: '已结算', value: 'settled' },       // [6]
]

// 计算属性：当前的状态选项
const currentStatusOptions = computed(() => {
    return currentType.value === 'publish' ? publishStatusOptions : acceptStatusOptions
})

const currentStatusIndex = ref(0)

// 数据列表
const demandList = ref<any[]>([])
const publishCount = ref(0)
const acceptCount = ref(0)
const loading = ref(false)

// 格式化位置
const formatLocation = (item: any) => {
    if (item.community_name && item.address) {
        return item.community_name + ' ' + item.address
    }
    return item.community_name || item.address || ''
}

// 格式化时间
const formatTime = (time: string) => {
    if (!time) return ''
    // 2026-03-10 10:00:00 -> 发布于2026.03.10 10:00
    const date = time.split(' ')
    const dateStr = date[0]?.replace(/-/g, '.') || ''
    const timeStr = date[1]?.substring(0, 5) || ''
    return `发布于${dateStr} ${timeStr}`
}

// 切换类型
const switchType = (type: 'publish' | 'accept') => {
    if (currentType.value === type) return
    currentType.value = type
    currentStatusIndex.value = 0  // 重置状态筛选
    loadList()
}

// 切换状态
const switchStatus = (index: number) => {
    if (currentStatusIndex.value === index) return
    currentStatusIndex.value = index
    loadList()
}

// 加载列表
const loadList = async () => {
    loading.value = true
    try {
        const statusOption = currentStatusOptions.value[currentStatusIndex.value]
        const params: any = {
            type: currentType.value,
            page_no: 1,
            page_size: 100,  // 一次性加载
        }
        // 状态筛选由前端过滤，不传 status 参数

        const res = await getMyDemandList(params)
        let list = res.list || []

        // 前端状态过滤
        if (statusOption.value === 'publishing') {
            list = list.filter((item: any) => [1, 2].includes(item.status))
        } else if (statusOption.value === 'completed') {
            // 发布的已完成 = 3 或 6，承接的已完成 = 3
            list = list.filter((item: any) => currentType.value === 'publish'
                ? [3, 6].includes(item.status)
                : item.status === 3)
        } else if (statusOption.value === 'offline') {
            list = list.filter((item: any) => [4, 5].includes(item.status))
        } else if (statusOption.value === 'ongoing') {
            list = list.filter((item: any) => item.status === 2)
        } else if (statusOption.value === 'settled') {
            list = list.filter((item: any) => item.status === 6)
        }

        demandList.value = list

        // 更新统计
        if (currentType.value === 'publish') {
            publishCount.value = (res.list || []).length
        } else {
            acceptCount.value = (res.list || []).length
        }
    } catch (e) {
        console.error('加载列表失败', e)
        demandList.value = []
    } finally {
        loading.value = false
    }
}

// 加载统计数据
const loadStats = async () => {
    try {
        // 并行加载两个类型的统计数据
        const [publishRes, acceptRes] = await Promise.all([
            getMyDemandList({ type: 'publish', page_size: 100 }),
            getMyDemandList({ type: 'accept', page_size: 100 }),
        ])

        publishCount.value = (publishRes.list || []).length
        acceptCount.value = (acceptRes.list || []).length
    } catch (e) {
        console.error('加载统计失败', e)
    }
}

// 操作按钮点击 - 跳转详情页
const handleAction = (item: any) => {
    uni.navigateTo({
        url: `/pages/demand/demand?id=${item.id}`
    })
}

// 地址点击 - 跳转详情页
const handleLocation = (item: any) => {
    uni.navigateTo({
        url: `/pages/demand/demand?id=${item.id}`
    })
}

// 页面显示时刷新数据（因为是常驻tabbar页面）
onShow(() => {
    loadStats()
    loadList()
})
</script>

<style lang="scss" scoped>
.mutual-aid-list-page {
    background-color: #F6F6F6;
    min-height: 100vh;
    padding: 20rpx 30rpx;
    padding-bottom: calc(120rpx + env(safe-area-inset-bottom));
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

// 加载中
.loading-wrap {
    display: flex;
    justify-content: center;
    padding: 60rpx 0;
}

.loading-text {
    font-size: 28rpx;
    color: #9CA6A6;
}

// 空状态
.empty-wrap {
    display: flex;
    justify-content: center;
    padding: 100rpx 0;
}

.empty-text {
    font-size: 28rpx;
    color: #9CA6A6;
}
</style>
