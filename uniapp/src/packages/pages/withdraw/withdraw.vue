<template>
    <view class="withdraw">
        <!-- 可提现金额 -->
        <view class="balance-section">
            <text class="balance-label">可提现金额（元）</text>
            <view class="balance-row">
                <text class="balance-yen">¥</text>
                <text class="balance-amount">{{ balance }}</text>
            </view>
        </view>

        <!-- 表单卡片 -->
        <view class="form-card">
            <text class="form-label">提现金额</text>
            <view class="amount-row">
                <text class="amount-yen">¥</text>
                <input
                    class="amount-input"
                    type="digit"
                    v-model="amount"
                    placeholder="0.00"
                    placeholder-style="color: #DADADA"
                />
                <text class="withdraw-all" @click="handleWithdrawAll">全部提现</text>
            </view>
            <view class="line" />
            <view class="fee-row">
                <text class="fee-label">手续费(3%)</text>
                <text class="fee-value">{{ feeText }}</text>
            </view>
            <view class="bank-row" @click="handleGoBankCard">
                <text class="bank-label">提现至</text>
                <view class="bank-right">
                    <text class="bank-value">{{ bankText }}</text>
                    <view class="bank-arrow" />
                </view>
            </view>
        </view>

        <!-- 说明 -->
        <view class="desc">
            说明：提现将产生3%手续费，提交申请后提现金额会在T+2周期内打款到上传的银行卡内，请您耐心等待。
        </view>

        <!-- 提交按钮 -->
        <view class="submit-btn" @click="handleSubmit">立即提现</view>
    </view>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import { getUserBankcardDetail } from '@/api/userBankcard'
import { getBillLists, withdrawBill } from '@/api/bill'

const balance = ref('0.00')
const amount = ref('')
const submitting = ref(false)
// 银行卡状态: null=未提交, 0=待审核, 1=已通过, 2=已拒绝
const bankCardStatus = ref<number | null>(null)

const feeText = computed(() => {
    const val = Number(amount.value)
    if (!val || val <= 0) return '输入金额自动显示'
    const fee = (val * 0.03).toFixed(2)
    return `¥${fee}`
})

const bankText = computed(() => {
    if (bankCardStatus.value === 1) return '已上传银行卡'
    return '上传银行卡'
})

const fetchBalance = async () => {
    try {
        const res = await getBillLists()
        balance.value = res.balance || '0.00'
    } catch (e) {
        console.error('获取余额失败', e)
    }
}

const fetchBankCardStatus = async () => {
    try {
        const res = await getUserBankcardDetail()
        if (res && res.id) {
            bankCardStatus.value = res.status
        } else {
            bankCardStatus.value = null
        }
    } catch (e) {
        console.error('获取银行卡信息失败', e)
    }
}

const handleWithdrawAll = () => {
    amount.value = balance.value
}

const handleGoBankCard = () => {
    uni.navigateTo({ url: '/packages/pages/upload-bankcard/upload-bankcard' })
}

const handleSubmit = async () => {
    if (!amount.value || Number(amount.value) <= 0) {
        uni.showToast({ title: '请输入提现金额', icon: 'none' })
        return
    }
    if (Number(amount.value) > Number(balance.value)) {
        uni.showToast({ title: '余额不足', icon: 'none' })
        return
    }
    if (bankCardStatus.value !== 1) {
        uni.navigateTo({ url: '/packages/pages/upload-bankcard/upload-bankcard' })
        return
    }
    if (submitting.value) return
    submitting.value = true
    try {
        await withdrawBill(Number(amount.value))
        uni.showToast({ title: '提现申请已提交', icon: 'none' })
        setTimeout(() => {
            uni.navigateBack()
        }, 1500)
    } catch (error: any) {
        uni.showToast({ title: error || '提现失败', icon: 'none' })
    } finally {
        submitting.value = false
    }
}

onShow(() => {
    fetchBalance()
    fetchBankCardStatus()
})
</script>

<style lang="scss" scoped>
.withdraw {
    min-height: 100vh;
    background-color: #F6F6F6;
    padding: 29rpx;
}

.balance-section {
    background-color: #F3FAFA;
    border-radius: 29rpx;
    padding: 36rpx 29rpx;
    margin-bottom: 29rpx;
}

.balance-label {
    font-size: 29rpx;
    color: #616B6B;
    margin-bottom: 18rpx;
}

.balance-row {
    display: flex;
    align-items: baseline;
}

.balance-yen {
    font-size: 36rpx;
    font-weight: 500;
    color: #222929;
    margin-right: 4rpx;
}

.balance-amount {
    font-size: 58rpx;
    font-weight: 700;
    color: #222929;
}

.form-card {
    background-color: #fff;
    border-radius: 22rpx;
    padding: 36rpx 29rpx;
    margin-bottom: 29rpx;
}

.form-label {
    font-size: 29rpx;
    color: #222929;
    margin-bottom: 18rpx;
}

.amount-row {
    display: flex;
    align-items: center;
}

.amount-yen {
    font-size: 36rpx;
    font-weight: 500;
    color: #222929;
    margin-right: 8rpx;
}

.amount-input {
    flex: 1;
    font-size: 51rpx;
    font-weight: 700;
    color: #222929;
    height: 70rpx;
}

.withdraw-all {
    font-size: 29rpx;
    color: #00A2A0;
    flex-shrink: 0;
}

.line {
    height: 1rpx;
    background-color: #EAEAEA;
    margin: 25rpx 0;
}

.fee-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36rpx;
}

.bank-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.fee-label,
.bank-label {
    font-size: 29rpx;
    color: #222929;
}

.fee-value {
    font-size: 29rpx;
    color: #999;
}

.bank-right {
    display: flex;
    align-items: center;
}

.bank-value {
    font-size: 29rpx;
    color: #222929;
}

.bank-arrow {
    width: 12rpx;
    height: 12rpx;
    border-top: 3rpx solid #9CA6A6;
    border-right: 3rpx solid #9CA6A6;
    transform: rotate(45deg);
    margin-left: 8rpx;
}

.desc {
    font-size: 25rpx;
    color: #616B6B;
    line-height: 40rpx;
    padding: 0 7rpx;
    margin-bottom: 100rpx;
}

.submit-btn {
    height: 102rpx;
    line-height: 102rpx;
    text-align: center;
    background-color: #00B6B4;
    border-radius: 51rpx;
    font-size: 36rpx;
    font-weight: 500;
    color: #fff;
}
</style>
