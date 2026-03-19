<template>
    <view class="my-bill">

        <!-- 账单信息卡片 -->
        <view class="bill-card">
            <view class="card-left">
                <view class="card-title">我的账单</view>
                <view class="card-label">我的收入（元）</view>
                <view class="card-amount">{{ income }}</view>
            </view>
            <view class="card-right">
                <navigator
                    class="withdraw-link"
                    url="/packages/pages/withdraw-detail/withdraw-detail"
                    hover-class="none"
                >
                    提现详情
                    <image class="arrow-icon" src="./assets/img/icon_arrow_right.png" />
                </navigator>
                <view class="card-label">我的支出（元）</view>
                <view class="card-amount">{{ expense }}</view>
            </view>
        </view>

        <!-- 立即提现按钮 -->
        <view class="withdraw-btn" @click="handleWithdraw">立即提现</view>

        <!-- Tab 切换 -->
        <view class="tabs-wrap">
            <u-tabs
                :list="tabList"
                :is-scroll="false"
                v-model="current"
                activeColor="#222222"
                inactiveColor="#777777"
                bgColor="transparent"
                lineColor="#00D4D1"
                @change="changeTab"
            ></u-tabs>
        </view>

        <!-- 账单列表 -->
        <view class="bill-list">
            <view
                class="bill-item"
                v-for="item in billList"
                :key="item.id"
            >
                <view class="item-info">
                    <text class="item-title">{{ item.title }}</text>
                    <text class="item-time">{{ item.time }}</text>
                </view>
                <view class="item-amount-wrap">
                    <text class="item-amount" :class="{ income: item.type === 1 }">
                        {{ item.type === 1 ? '+' : '-' }}¥{{ item.amount }}
                    </text>
                    <text class="item-status" :class="{ pending: item.status === 0 }">
                        {{ item.status === 1 ? '已入账' : '待入账' }}
                    </text>
                </view>
            </view>

            <!-- 空状态 -->
            <view v-if="billList.length === 0" class="empty-state">
                <text class="empty-text">暂无账单记录</text>
            </view>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import { getBillLists } from '@/api/bill'

// 收入支出金额
const income = ref('0.00')
const expense = ref('0.00')

// Tab 配置
const tabList = ref([
    { name: '收入详情' },
    { name: '支出详情' }
])
const current = ref(0)

// 原始账单列表
const allBillList = ref<any[]>([])

// 根据Tab筛选后的账单列表
const billList = computed(() => {
    const type = current.value === 0 ? 1 : 2  // 0=收入详情(type=1), 1=支出详情(type=2)
    return allBillList.value.filter(item => item.type === type)
})

// 加载账单数据
const loadBillData = async () => {
    try {
        const res = await getBillLists()
        income.value = res.income || '0.00'
        expense.value = res.expense || '0.00'
        allBillList.value = res.list || []
    } catch (e) {
        console.error('获取账单数据失败', e)
    }
}

// 切换 Tab
const changeTab = (index: number) => {
    current.value = index
}

// 立即提现
const handleWithdraw = () => {
    uni.showToast({
        title: '提现功能开发中',
        icon: 'none'
    })
}

// 页面显示时加载数据
onShow(() => {
    loadBillData()
})
</script>

<style lang="scss" scoped>
.my-bill {
    min-height: 100vh;
    position: relative;
}

.bg-image {
    // 不再需要单独的背景图片元素，使用 CSS background
    display: none;
}

.bill-card {
    position: relative;
    z-index: 1;
    margin: 30rpx 29rpx;
    padding: 29rpx;
    background-color: #fff;
    border-radius: 29rpx;
    display: flex;
    justify-content: space-between;
}

.card-left {
    .card-title {
        font-size: 29rpx;
        font-weight: 500;
        color: #222;
        margin-bottom: 22rpx;
    }
}

.card-right {
    text-align: right;
}

.card-label {
    font-size: 29rpx;
    color: #666;
    margin-bottom: 4rpx;
}

.card-amount {
    font-size: 36rpx;
    font-weight: 500;
    color: #000;
}

.withdraw-link {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    font-size: 24rpx;
    color: #999;
    margin-bottom: 27rpx;

    .arrow-icon {
        width: 9rpx;
        height: 15rpx;
        margin-left: 8rpx;
    }
}

.withdraw-btn {
    position: relative;
    z-index: 1;
    margin: 0 29rpx 36rpx;
    height: 102rpx;
    line-height: 102rpx;
    text-align: center;
    background-color: #00B6B4;
    border-radius: 51rpx;
    font-size: 36rpx;
    font-weight: 500;
    color: #fff;
}

.tabs-wrap {
    position: relative;
    z-index: 1;
    background-color: transparent;

    :deep(.u-tabs) {
        background-color: transparent;
    }

    :deep(.u-tabs__wrapper__nav) {
        background-color: transparent;
    }

    :deep(.u-tabs__wrapper__nav__line) {
        background-color: #00D4D1;
    }
}

.bill-list {
    position: relative;
    z-index: 1;
    padding: 0 29rpx;
}

.bill-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 36rpx 29rpx;
    margin-bottom: 22rpx;
    background-color: #fff;
    border-radius: 29rpx;
}

.item-info {
    display: flex;
    flex-direction: column;
    gap: 4rpx;
}

.item-title {
    font-size: 33rpx;
    font-weight: 500;
    color: #333;
}

.item-time {
    font-size: 25rpx;
    color: #999;
}

.item-amount-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4rpx;
}

.item-amount {
    font-size: 33rpx;
    font-weight: 500;
    color: #00A2A0;

    &.income {
        color: #00A2A0;
    }
}

.item-status {
    font-size: 25rpx;
    font-weight: 500;
    color: #999;

    &.pending {
        color: #FF7E22;
    }
}

.empty-state {
    padding: 100rpx 0;
    text-align: center;
}

.empty-text {
    font-size: 28rpx;
    color: #999;
}
</style>
