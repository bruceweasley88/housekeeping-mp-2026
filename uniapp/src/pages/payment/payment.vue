<template>
  <view class="payment-page">
    <!-- 支付金额区域 -->
    <view class="amount-section">
      <text class="amount-label">支付金额</text>
      <view class="amount-value-wrapper">
        <text class="amount-currency">¥</text>
        <text class="amount-value">360.00</text>
      </view>
      <view class="countdown-wrapper">
        <text class="countdown-label">剩余支付时间</text>
        <text class="countdown-value">14:58</text>
      </view>
    </view>

    <!-- 支付明细 -->
    <view class="detail-section">
      <view class="detail-item">
        <text class="detail-label">支付金额</text>
        <text class="detail-value">¥340.00</text>
      </view>
      <view class="detail-item">
        <text class="detail-label">紧急服务费</text>
        <text class="detail-value">¥20.00</text>
      </view>
    </view>

    <!-- 支付方式 -->
    <view class="payway-section">
      <text class="payway-label">付款方式</text>
      <view class="payway-selected">
        <image
          class="payway-icon"
          src="./assets/img/icon_wechat.png"
          mode="aspectFit"
        />
        <text class="payway-name">微信支付</text>
      </view>
    </view>

    <!-- 立即支付按钮 -->
    <view class="submit-section">
      <view class="submit-btn" @click="handlePay">
        <text class="submit-text">立即支付</text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// 倒计时
const countdown = ref('14:58')

// 立即支付
const handlePay = () => {
  uni.showLoading({
    title: '正在支付...'
  })
  // TODO: 调用支付接口
  setTimeout(() => {
    uni.hideLoading()
    uni.redirectTo({
      url: '/pages/payment_result/payment_result'
    })
  }, 1500)
}
</script>

<style scoped lang="scss" src="./assets/payment.scss" />
