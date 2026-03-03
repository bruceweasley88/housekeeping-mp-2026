<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="add-community">
        <!-- 表单区域 -->
        <view class="form-section">
            <!-- 所在地 -->
            <view class="form-item">
                <text class="label">所在地</text>
                <view class="content">
                    <text class="placeholder">请选择所在地址</text>
                    <u-icon name="arrow-right" size="18" color="#CDCDCD" />
                </view>
            </view>

            <!-- 小区名称 -->
            <view class="form-item">
                <text class="label">小区名称</text>
                <view class="content">
                    <input
                        class="input"
                        v-model="communityName"
                        placeholder="请填写小区名称"
                        placeholder-class="placeholder-class"
                    />
                </view>
            </view>

            <!-- 申请理由 -->
            <view class="form-item">
                <text class="label">申请理由</text>
                <view class="content">
                    <input
                        class="input"
                        v-model="reason"
                        placeholder="例：小区需要互助等"
                        placeholder-class="placeholder-class"
                    />
                </view>
            </view>
        </view>

        <!-- 底部区域：提示文字 + 确认按钮 -->
        <view class="bottom-section">
            <view class="tips">
                <text class="tips-text">新增小区需要通过后台审核，审核后将开放</text>
            </view>
            <view class="submit-btn" @click="handleSubmit">确认申请</view>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

// 表单数据
const address = ref('')
const communityName = ref('')
const reason = ref('')

// 提交申请
const handleSubmit = () => {
    if (!address.value) {
        uni.showToast({
            title: '请选择所在地址',
            icon: 'none'
        })
        return
    }
    if (!communityName.value) {
        uni.showToast({
            title: '请填写小区名称',
            icon: 'none'
        })
        return
    }
    if (!reason.value) {
        uni.showToast({
            title: '请填写申请理由',
            icon: 'none'
        })
        return
    }

    uni.showToast({
        title: '申请已提交',
        icon: 'success'
    })
}
</script>

<style lang="scss" scoped>
.add-community {
    min-height: 100vh;
    background-color: #fff;
    padding: 0 36rpx;
}

.form-item {
    display: flex;
    align-items: center;
    padding-top: 51rpx;

    &:first-child {
        padding-top: 56rpx;
    }

    .label {
        width: 130rpx;
        font-size: 33rpx;
        font-weight: 500;
        color: #444;
        flex-shrink: 0;
    }

    .content {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-left: 67rpx;
        padding-bottom: 28rpx;
        border-bottom: 1rpx solid #F5F5F5;
    }

    .placeholder {
        font-size: 33rpx;
        color: #CDCDCD;
    }

    .input {
        flex: 1;
        font-size: 33rpx;
        color: #222;
    }

    .placeholder-class {
        color: #CDCDCD;
    }
}

.bottom-section {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 0 36rpx 50rpx;
    background-color: #fff;
}

.tips {
    text-align: center;
    margin-bottom: 22rpx;
}

.tips-text {
    font-size: 25rpx;
    color: #666;
}

.submit-btn {
    height: 102rpx;
    line-height: 102rpx;
    text-align: center;
    background: #00B6B4;
    border-radius: 51rpx;
    font-size: 36rpx;
    font-weight: 500;
    color: #fff;
}
</style>
