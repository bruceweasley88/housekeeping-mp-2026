<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="owner-verify">
        <!-- 实名认证 标题 -->
        <text class="section-title">实名认证</text>

        <!-- 身份证正面 -->
        <view class="idcard-wrapper" @click="handleCaptureFront">
            <image
                class="idcard-image"
                src="/packages/static/images/img_photoidz.png"
                mode="widthFix"
            />
        </view>

        <!-- 身份证反面 -->
        <view class="idcard-wrapper" @click="handleCaptureBack">
            <image
                class="idcard-image"
                src="/packages/static/images/img_photoidf.png"
                mode="widthFix"
            />
        </view>

        <!-- 业主和住户认证 标题区 -->
        <view class="section-header flex justify-between items-center">
            <text class="section-title">业主和住户认证</text>
            <text class="section-subtitle">水电费缴费截图、租房合同等</text>
        </view>

        <!-- 上传按钮卡片 -->
        <view class="upload-card" @click="handleUpload">
            <image
                class="upload-icon"
                src="/packages/static/images/img_upload.png"
                mode="aspectFit"
            />
            <text class="upload-text">立即上传</text>
        </view>

        <!-- 说明文字 -->
        <text class="description">业主认证通过后，才能发布和承接需求</text>

        <!-- 立即认证按钮 -->
        <view class="submit-btn" @click="handleSubmit">
            <text class="submit-btn-text">立即认证</text>
        </view>
    </view>
</template>

<script setup lang="ts">
const handleCaptureFront = () => {
    uni.chooseImage({
        count: 1,
        sourceType: ['camera'],
        success: (res) => {
            console.log('身份证正面:', res.tempFilePaths)
        }
    })
}

const handleCaptureBack = () => {
    uni.chooseImage({
        count: 1,
        sourceType: ['camera'],
        success: (res) => {
            console.log('身份证反面:', res.tempFilePaths)
        }
    })
}

const handleUpload = () => {
    uni.chooseImage({
        count: 9,
        sourceType: ['album', 'camera'],
        success: (res) => {
            console.log('业主认证材料:', res.tempFilePaths)
        }
    })
}

const handleSubmit = () => {
    // TODO: 实现认证提交逻辑
    console.log('立即认证')
}
</script>

<style lang="scss" scoped>
.owner-verify {
    min-height: 100vh;
    background-color: #f6f6f6;
    padding: 0 30rpx;
}

.section-title {
    display: block;
    font-size: 29rpx;
    font-weight: 500;
    color: #222222;
    margin-top: 40rpx;
    margin-bottom: 22rpx;
}

.idcard-wrapper {
    width: 692rpx;
    background-color: #ffffff;
    border-radius: 29rpx;
    margin-bottom: 22rpx;
    overflow: hidden;
}

.idcard-image {
    width: 692rpx;
    display: block;
}

.section-header {
    margin-top: 20rpx;
    margin-bottom: 22rpx;
}

.section-subtitle {
    font-size: 27rpx;
    color: #999999;
}

.upload-card {
    width: 146rpx;
    height: 146rpx;
    background-color: #ffffff;
    border-radius: 15rpx;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.upload-icon {
    width: 54rpx;
    height: 44rpx;
}

.upload-text {
    font-size: 25rpx;
    font-weight: 500;
    color: #444444;
    margin-top: 12rpx;
}

.description {
    display: block;
    font-size: 25rpx;
    color: #666666;
    margin-top: 80rpx;
    margin-left: 160rpx;
}

.submit-btn {
    margin-top: 22rpx;
    background-color: #00b6b4;
    border-radius: 51rpx;
    height: 102rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.submit-btn-text {
    font-size: 36rpx;
    font-weight: 500;
    color: #ffffff;
}
</style>
