<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="upload-bankcard">
        <!-- 您的姓名 表单项 -->
        <view class="form-section">
            <text class="form-label">您的姓名</text>
            <view class="form-item flex justify-between items-center">
                <text class="form-placeholder">真实姓名</text>
                <text class="form-value">{{ userName }}</text>
            </view>
        </view>

        <!-- 上传卡照片 标题 -->
        <text class="section-title">上传卡照片</text>

        <!-- 银行卡图片区域 -->
        <view class="bankcard-wrapper">
            <image
                class="bankcard-image"
                src="/packages/static/images/img_bankcard.png"
                mode="widthFix"
            />
            <!-- 拍摄按钮（覆盖在图片底部） -->
            <view class="capture-btn" @click="handleCapture">
                <text class="capture-btn-text">拍摄银行卡正面</text>
            </view>
        </view>

        <!-- 说明文字 -->
        <text class="description">
            说明：请拍照上传银行卡正面，人工审核成功后，您的银行卡信息将显示为"已上传"，提现将会打款到您上传的银行卡，请注意银行卡拍照清晰。
        </text>

        <!-- 确认上传按钮 -->
        <view class="submit-btn" @click="handleSubmit">
            <text class="submit-btn-text">确认上传</text>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const userName = ref('薛凯琪') // TODO: 从用户信息获取

const handleCapture = () => {
    uni.chooseImage({
        count: 1,
        sourceType: ['camera'],
        success: (res) => {
            console.log('选择的图片:', res.tempFilePaths)
        }
    })
}

const handleSubmit = () => {
    // TODO: 实现上传逻辑
    console.log('确认上传')
}
</script>

<style lang="scss" scoped>
.upload-bankcard {
    min-height: 100vh;
    background-color: #f6f6f6;
    padding: 0 30rpx;
}

.form-section {
    margin-top: 24rpx;
}

.form-label {
    display: block;
    font-size: 29rpx;
    color: #222222;
    margin-bottom: 22rpx;
}

.form-item {
    background-color: #ffffff;
    border-radius: 22rpx;
    height: 104rpx;
    padding: 0 29rpx;
}

.form-placeholder {
    font-size: 33rpx;
    color: #666666;
}

.form-value {
    font-size: 33rpx;
    color: #222222;
}

.section-title {
    display: block;
    font-size: 29rpx;
    color: #222222;
    margin-top: 36rpx;
    margin-bottom: 22rpx;
}

.bankcard-wrapper {
    position: relative;
    width: 692rpx;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.bankcard-image {
    width: 692rpx;
    display: block;
}

.capture-btn {
    position: absolute;
    bottom: 40rpx;
    left: 50%;
    transform: translateX(-50%);
    background-color: #00b6b4;
    border-radius: 36rpx;
    width: 291rpx;
    height: 66rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.capture-btn-text {
    font-size: 29rpx;
    color: #ffffff;
}

.description {
    display: block;
    font-size: 25rpx;
    color: #666666;
    line-height: 40rpx;
    margin-top: 25rpx;
}

.submit-btn {
    margin-top: 266rpx;
    background-color: #00b6b4;
    border-radius: 51rpx;
    height: 102rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.submit-btn-text {
    font-size: 36rpx;
    color: #ffffff;
}
</style>
