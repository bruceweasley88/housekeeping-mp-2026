<template>
    <view class="page">
        <!-- 状态卡片 -->
        <view class="status-card">
            <view class="status-icon">
                <view class="icon-circle">
                    <view class="check-mark"></view>
                </view>
            </view>
            <view class="status-info">
                <text class="status-title">需求已完成</text>
                <text class="status-desc">确认需求已完成，进行平台结算金额支付</text>
            </view>
        </view>

        <!-- 需求信息卡片 -->
        <view class="demand-card">
            <text class="demand-title">初三英语家教2小时</text>
            <view class="demand-meta">
                <view class="type-tag">
                    <text class="type-text">陪伴需求</text>
                </view>
                <text class="price-symbol">¥</text>
                <text class="price-value">80.00</text>
                <text class="price-unit">/小时</text>
            </view>
        </view>

        <!-- 需求内容区块 -->
        <view class="content-card">
            <view class="section-header">
                <view class="dot-indicator"></view>
                <text class="section-title">需求内容</text>
            </view>
            <view class="detail-row">
                <text class="detail-label">需求详情</text>
                <text class="detail-text">需要找小学三年级数学家教，女性最佳，要有教学经验，周六日两天晚上19:00-21:00上课，合适的可长期。</text>
            </view>
            <view class="photo-row">
                <text class="detail-label">照片信息</text>
                <image class="photo-img" src="/static/images/publish/icon_successb.png" mode="aspectFill" />
            </view>
        </view>

        <!-- 发布人信息区块 -->
        <view class="publisher-card">
            <view class="section-header">
                <view class="dot-indicator"></view>
                <text class="section-title">发布人信息</text>
            </view>
            <view class="publisher-info">
                <view class="publisher-row">
                    <image class="avatar" src="/static/images/publish/icon_successb.png" mode="aspectFill" />
                    <view class="publisher-text">
                        <text class="publisher-name">小伟妈妈</text>
                        <text class="publisher-time">发布于 2026.01.02 12:00</text>
                    </view>
                    <view class="contact-btn">
                        <text class="contact-text">立即沟通</text>
                    </view>
                </view>
            </view>
            <view class="info-row">
                <text class="info-label">联系电话</text>
                <text class="info-value">134 2989 1277</text>
            </view>
            <view class="info-row">
                <text class="info-label">小区地址</text>
                <text class="info-value">新港东琶洲新村A12栋</text>
            </view>
        </view>

        <!-- 承接人信息区块 -->
        <view class="receiver-card">
            <view class="section-header">
                <view class="dot-indicator"></view>
                <text class="section-title">承接人信息</text>
            </view>
            <view class="receiver-info">
                <view class="receiver-row">
                    <image class="avatar" src="/static/images/publish/icon_successb.png" mode="aspectFill" />
                    <view class="receiver-text">
                        <text class="receiver-name">鱼微微</text>
                        <text class="receiver-time">承接于 2026.01.02 12:47</text>
                    </view>
                    <view class="contact-btn">
                        <text class="contact-text">立即沟通</text>
                    </view>
                </view>
            </view>
        </view>

        <!-- 底部操作栏 -->
        <view class="bottom-bar">
            <view class="adjust-btn">
                <text class="adjust-text">调整金额</text>
            </view>
            <view class="settle-btn">
                <text class="settle-text">立即结算</text>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

</script>

<style lang="scss" scoped>
.page {
    min-height: 100vh;
    background: #F3FAFA;
    padding-bottom: calc(145rpx + env(safe-area-inset-bottom));
}

// 状态卡片
.status-card {
    background: #fff;
    border-radius: 29rpx;
    margin: 29rpx;
    padding: 29rpx;
    display: flex;
    align-items: center;
}

.status-icon {
    width: 80rpx;
    height: 80rpx;
    background: #F3FAFA;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.icon-circle {
    width: 47rpx;
    height: 47rpx;
    border: 4rpx solid #222222;
    border-radius: 50%;
    position: relative;

    .check-mark {
        position: absolute;
        left: 14rpx;
        top: 7rpx;
        width: 14rpx;
        height: 24rpx;
        border-right: 4rpx solid #222222;
        border-bottom: 4rpx solid #222222;
        transform: rotate(45deg);
    }
}

.status-info {
    margin-left: 13rpx;
    flex: 1;
}

.status-title {
    font-size: 36rpx;
    font-weight: 500;
    color: #222222;
    display: block;
}

.status-desc {
    font-size: 25rpx;
    color: #666666;
    margin-top: 7rpx;
    display: block;
}

// 需求信息卡片
.demand-card {
    background: #fff;
    border-radius: 29rpx;
    margin: 0 29rpx 22rpx;
    padding: 29rpx;
}

.demand-title {
    font-size: 33rpx;
    font-weight: 500;
    color: #222222;
    display: block;
}

.demand-meta {
    display: flex;
    align-items: center;
    margin-top: 16rpx;
}

.type-tag {
    background: #F3FAFA;
    border-radius: 11rpx;
    padding: 7rpx 14rpx;
    margin-right: auto;
}

.type-text {
    font-size: 25rpx;
    font-weight: 500;
    color: #3A9B9C;
}

.price-symbol {
    font-size: 33rpx;
    font-weight: bold;
    color: #F04530;
}

.price-value {
    font-size: 44rpx;
    font-weight: bold;
    color: #F04530;
    margin-left: 4rpx;
}

.price-unit {
    font-size: 25rpx;
    font-weight: bold;
    color: #F04530;
    margin-left: 5rpx;
}

// 需求内容区块
.content-card {
    background: #fff;
    border-radius: 29rpx;
    margin: 0 29rpx 22rpx;
    padding: 29rpx;
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 22rpx;
}

.dot-indicator {
    width: 15rpx;
    height: 15rpx;
    background: #00B6B4;
    border-radius: 50%;
    position: relative;

    &::after {
        content: '';
        position: absolute;
        width: 15rpx;
        height: 15rpx;
        background: rgba(125, 255, 254, 0.4);
        border-radius: 50%;
    }
}

.section-title {
    font-size: 29rpx;
    font-weight: 500;
    color: #222222;
    margin-left: 11rpx;
}

.detail-row {
    display: flex;
    margin-bottom: 22rpx;
}

.detail-label {
    font-size: 29rpx;
    color: #666666;
    width: 130rpx;
    flex-shrink: 0;
    white-space: nowrap;
    margin-right: 18rpx;
}

.detail-text {
    font-size: 29rpx;
    color: #222222;
    flex: 1;
    line-height: 40rpx;
}

.photo-row {
    display: flex;
    align-items: flex-start;
}

.photo-img {
    width: 156rpx;
    height: 116rpx;
    border-radius: 11rpx;
    margin-top: 7rpx;
}

// 发布人信息区块
.publisher-card {
    background: #fff;
    border-radius: 29rpx;
    margin: 0 29rpx 22rpx;
    padding: 29rpx;
}

.publisher-info {
    background: #F3FAFA;
    border-radius: 15rpx;
    padding: 22rpx;
}

.publisher-row {
    display: flex;
    align-items: center;
}

.publisher-text {
    margin-left: 14rpx;
    flex: 1;
}

.publisher-name {
    font-size: 29rpx;
    font-weight: 500;
    color: #333333;
    display: block;
}

.publisher-time {
    font-size: 25rpx;
    color: #999999;
    display: block;
    margin-top: 4rpx;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-top: 22rpx;
}

.info-label {
    font-size: 29rpx;
    color: #616B6B;
}

.info-value {
    font-size: 29rpx;
    color: #222222;
}

// 承接人信息区块
.receiver-card {
    background: #fff;
    border-radius: 29rpx;
    margin: 0 29rpx;
    padding: 29rpx;
}

.receiver-info {
    background: #F3FAFA;
    border-radius: 15rpx;
    padding: 22rpx;
}

.receiver-row {
    display: flex;
    align-items: center;
}

.avatar {
    width: 62rpx;
    height: 62rpx;
    border-radius: 50%;
    flex-shrink: 0;
}

.receiver-text {
    margin-left: 14rpx;
    flex: 1;
}

.receiver-name {
    font-size: 29rpx;
    font-weight: 500;
    color: #333333;
    display: block;
}

.receiver-time {
    font-size: 25rpx;
    color: #999999;
    display: block;
    margin-top: 4rpx;
}

.contact-btn {
    background: #fff;
    border: 2rpx solid #00B6B4;
    border-radius: 60rpx;
    padding: 14rpx 22rpx;
}

.contact-text {
    font-size: 24rpx;
    font-weight: 500;
    color: #00B6B4;
}

// 底部操作栏
.bottom-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    padding: 22rpx 36rpx calc(22rpx + env(safe-area-inset-bottom));
    display: flex;
    gap: 22rpx;
}

.adjust-btn {
    width: 236rpx;
    height: 98rpx;
    background: #F6F6F6;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.adjust-text {
    font-size: 29rpx;
    color: #999999;
}

.settle-btn {
    flex: 1;
    height: 98rpx;
    background: #00B6B4;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.settle-text {
    font-size: 33rpx;
    font-weight: 500;
    color: #fff;
}
</style>