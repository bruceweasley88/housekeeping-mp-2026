<template>
    <view class="user-page">
        <!-- 顶部背景区域 -->
        <view class="header-section">
            <!-- 用户信息 -->
            <view class="user-info">
                <image
                    class="avatar"
                    :src="userInfo.avatar || '/static/images/user/default_avatar.png'"
                    mode="aspectFill"
                />
                <view class="user-meta">
                    <text class="nickname">{{ userInfo.nickname || '未登录' }}</text>
                    <view class="certified-tag" :class="{ 'not-certified': verifyStatus !== 1 }">
                        <text class="certified-text">{{ verifyStatus === 1 ? '已认证业主' : '未认证' }}</text>
                    </view>
                </view>
            </view>

            <!-- 账单卡片 -->
            <view class="bill-card">
                <view class="bill-header">
                    <text class="bill-title">我的账单</text>
                    <view class="bill-detail" @click="goToBill">
                        <text class="detail-text">账单详情</text>
                        <image class="arrow-icon" src="./assets/img/icon_arrow_right.png" mode="aspectFit" />
                    </view>
                </view>
                <view class="bill-content">
                    <view class="income-section">
                        <text class="label">我的收入（元）</text>
                        <view class="amount-row">
                            <text class="amount">{{ billIncome }}</text>
                            <view class="withdraw-btn" @click="goToWallet">
                                <text class="withdraw-text">去提现</text>
                                <image class="arrow-white" src="./assets/img/icon_arrow_white.png" mode="aspectFit" />
                            </view>
                        </view>
                    </view>
                    <view class="expense-section">
                        <text class="label">我的支出（元）</text>
                        <text class="amount">{{ billExpense }}</text>
                    </view>
                </view>
            </view>
        </view>

        <!-- 设置列表 -->
        <view class="settings-section">
            <text class="section-title">我的设置</text>
            <view class="settings-list">
                <view class="setting-item" @click="goToProfile">
                    <image class="setting-icon" src="./assets/img/icon_profile.png" mode="aspectFit" />
                    <text class="setting-text">我的资料</text>
                    <image class="arrow-icon" src="./assets/img/icon_arrow_right.png" mode="aspectFit" />
                </view>
                <view class="setting-item" @click="goToAddCommunity">
                    <image class="setting-icon" src="./assets/img/icon_myhouse.png" mode="aspectFit" />
                    <text class="setting-text">新增小区</text>
                    <image class="arrow-icon" src="./assets/img/icon_arrow_right.png" mode="aspectFit" />
                </view>
                <view class="setting-item" @click="goToOwnerVerify">
                    <image class="setting-icon" src="./assets/img/icon_accredit.png" mode="aspectFit" />
                    <text class="setting-text">业主认证</text>
                    <image class="arrow-icon" src="./assets/img/icon_arrow_right.png" mode="aspectFit" />
                </view>
                <view class="setting-item" @click="goToAboutUs">
                    <image class="setting-icon" src="./assets/img/icon_aboutme.png" mode="aspectFit" />
                    <text class="setting-text">关于我们</text>
                    <image class="arrow-icon" src="./assets/img/icon_arrow_right.png" mode="aspectFit" />
                </view>
                <view class="setting-item" @click="handleCustomerService">
                    <image class="setting-icon" src="./assets/img/icon_service.png" mode="aspectFit" />
                    <text class="setting-text">联系客服</text>
                    <image class="arrow-icon" src="./assets/img/icon_arrow_right.png" mode="aspectFit" />
                </view>
            </view>
        </view>

        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'
import { onShow } from '@dcloudio/uni-app'
import { storeToRefs } from 'pinia'
import { getUserVerifyDetail } from '@/api/userVerify'
import { getBillLists } from '@/api/bill'
import { goToCustomerService } from '@/utils/util'

const userStore = useUserStore()
const { userInfo } = storeToRefs(userStore)

// 认证状态: null=未提交, 0=待审核, 1=已通过, 2=已拒绝
const verifyStatus = ref<number | null>(null)

// 账单收入支出
const billIncome = ref('0.00')
const billExpense = ref('0.00')

onShow(async () => {
    userStore.getUser()
    // 获取认证状态
    try {
        const res = await getUserVerifyDetail()
        verifyStatus.value = res?.status ?? null
    } catch (e) {
        verifyStatus.value = null
    }
    // 获取账单汇总
    try {
        const billRes = await getBillLists()
        billIncome.value = billRes?.income || '0.00'
        billExpense.value = billRes?.expense || '0.00'
    } catch (e) {
        billIncome.value = '0.00'
        billExpense.value = '0.00'
    }
})

// 跳转账单详情
const goToBill = () => {
    uni.navigateTo({
        url: '/packages/pages/my-bill/my-bill'
    })
}

// 跳转钱包/提现
const goToWallet = () => {
    uni.navigateTo({
        url: '/packages/pages/my-bill/my-bill'
    })
}

// 跳转我的资料
const goToProfile = () => {
    uni.navigateTo({
        url: '/packages/pages/my-profile/my-profile'
    })
}

// 跳转新增小区
const goToAddCommunity = () => {
    uni.navigateTo({
        url: '/packages/pages/add-community/add-community'
    })
}

// 跳转业主认证
const goToOwnerVerify = () => {
    uni.navigateTo({
        url: '/packages/pages/owner-verify/owner-verify'
    })
}

// 跳转关于我们
const goToAboutUs = () => {
    uni.navigateTo({
        url: '/packages/pages/about-us/about-us'
    })
}

// 跳转联系客服
const handleCustomerService = () => {
    goToCustomerService()
}
</script>

<style lang="scss" scoped>
.user-page {
    min-height: 100vh;
    background: linear-gradient( 90deg, #C3FBEF 0%, #C0F6FD 100%);
    padding-bottom: calc(120rpx + env(safe-area-inset-bottom));
}

// 顶部区域
.header-section {
    padding: 170rpx 30rpx 0;
}

// 用户信息
.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 60rpx;

    .avatar {
        width: 110rpx;
        height: 110rpx;
        border-radius: 50%;
        margin-right: 24rpx;
        background-color: #fff;
    }

    .user-meta {
        display: flex;
        flex-direction: column;
    }

    .nickname {
        font-size: 36rpx;
        font-weight: 500;
        color: #222929;
        margin-bottom: 12rpx;
    }

    .certified-tag {
        background-color: #FF7E22;
        border-radius: 8rpx;
        padding: 4rpx 12rpx;
        align-self: flex-start;

        &.not-certified {
            background-color: #9CA6A6;
        }

        .certified-text {
            font-size: 24rpx;
            color: #fff;
        }
    }
}

// 账单卡片
.bill-card {
    background-color: #fff;
    border-radius: 24rpx;
    padding: 30rpx;
    margin-bottom: 30rpx;

    .bill-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30rpx;

        .bill-title {
            font-size: 32rpx;
            font-weight: 500;
            color: #222929;
        }

        .bill-detail {
            display: flex;
            align-items: center;

            .detail-text {
                font-size: 26rpx;
                color: #9CA6A6;
                margin-right: 8rpx;
            }

            .arrow-icon {
                width: 10rpx;
                height: 16rpx;
            }
        }
    }

    .bill-content {
        display: flex;
        justify-content: space-between;
    }

    .income-section {
        flex: 1;

        .label {
            display: block;
            font-size: 28rpx;
            color: #616B6B;
            margin-bottom: 16rpx;
        }

        .amount-row {
            display: flex;
            align-items: center;
        }

        .amount {
            font-size: 40rpx;
            font-weight: 500;
            color: #222929;
            margin-right: 20rpx;
        }

        .withdraw-btn {
            display: flex;
            align-items: center;
            background-color: #00B6B4;
            border-radius: 24rpx;
            padding: 8rpx 16rpx;

            .withdraw-text {
                font-size: 22rpx;
                color: #fff;
                margin-right: 6rpx;
            }

            .arrow-white {
                width: 10rpx;
                height: 16rpx;
            }
        }
    }

    .expense-section {
        width: 200rpx;

        .label {
            display: block;
            font-size: 28rpx;
            color: #616B6B;
            margin-bottom: 16rpx;
        }

        .amount {
            font-size: 40rpx;
            font-weight: 500;
            color: #222929;
        }
    }
}

// 设置区域
.settings-section {
    padding: 0 30rpx;

    .section-title {
        display: block;
        font-size: 32rpx;
        font-weight: 500;
        color: #222929;
        margin-bottom: 24rpx;
        margin-left: 10rpx;
    }
}

// 设置列表
.settings-list {
    background-color: #fff;
    border-radius: 24rpx;
    padding: 0 30rpx;

    .setting-item {
        display: flex;
        align-items: center;
        height: 100rpx;
        border-bottom: 1rpx solid #F0F0F0;

        &:last-child {
            border-bottom: none;
        }

        .setting-icon {
            width: 40rpx;
            height: 40rpx;
            margin-right: 24rpx;
        }

        .icon-placeholder {
            width: 40rpx;
            height: 40rpx;
            margin-right: 24rpx;
            border-radius: 8rpx;

            &.community-icon {
                border: 2rpx solid #222929;
            }

            &.about-icon {
                border: 2rpx solid #222929;
                border-radius: 50%;
            }

            &.service-icon {
                border: 2rpx solid #222929;
                border-radius: 8rpx;
            }
        }

        .setting-text {
            flex: 1;
            font-size: 30rpx;
            color: #222929;
        }

        .arrow-icon {
            width: 10rpx;
            height: 16rpx;
        }
    }
}
</style>
