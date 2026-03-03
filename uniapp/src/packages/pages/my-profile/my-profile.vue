<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="my-profile">
        <!-- 头像区域 -->
        <view class="avatar-section">
            <view class="avatar-wrap">
                <view class="avatar-inner">
                    <image
                        class="avatar-img"
                        :src="avatar || defaultAvatar"
                        mode="aspectFill"
                    />
                </view>
                <view class="avatar-border"></view>
                <view class="camera-btn">
                    <image
                        class="camera-icon"
                        src="./assets/img/icon_camera.png"
                        mode="aspectFit"
                    />
                </view>
            </view>
        </view>

        <!-- 表单区域 -->
        <view class="form-section">
            <!-- 昵称 -->
            <view class="form-item">
                <text class="label">昵称</text>
                <view class="content">
                    <text class="value">{{ nickname || '轻松熊' }}</text>
                </view>
            </view>

            <!-- 性别选择 -->
            <view class="form-item">
                <text class="label">选择性别</text>
                <view class="content">
                    <view class="gender-btns">
                        <view
                            class="gender-btn"
                            :class="{ active: gender === 1 }"
                            @click="gender = 1"
                        >
                            男
                        </view>
                        <view
                            class="gender-btn"
                            :class="{ active: gender === 2 }"
                            @click="gender = 2"
                        >
                            女
                        </view>
                    </view>
                </view>
            </view>

            <!-- 电话号码 -->
            <view class="form-item">
                <text class="label">电话号码</text>
                <view class="content">
                    <input
                        class="input"
                        v-model="phone"
                        type="number"
                        placeholder="填写您的电话号码"
                        placeholder-class="placeholder-class"
                    />
                </view>
            </view>

            <!-- 地址 -->
            <view class="form-item address-item">
                <text class="label">你的地址</text>
                <view class="content">
                    <text class="address-value">
                        广东省广州市海珠区 保利天悦小区A18栋203
                    </text>
                    <view class="arrow-icon">
                        <u-icon name="arrow-right" size="18" color="#CDCDCD" />
                    </view>
                </view>
            </view>
        </view>

        <!-- 隐私协议 -->
        <view class="agreement-section">
            <view class="checkbox-wrap" @click="agreed = !agreed">
                <view class="checkbox" :class="{ checked: agreed }">
                    <u-icon v-if="agreed" name="checkmark" size="12" color="#fff" />
                </view>
            </view>
            <text class="agreement-text">
                <text class="text-gray">已阅读并且同意</text>
                <text class="text-link">《用户隐私协议》</text>
                <text class="text-gray">，用户数据将用于个人信息展示与功能正常使用。</text>
            </text>
        </view>

        <!-- 保存按钮 -->
        <view class="save-btn" @click="handleSave">保存</view>
    </view>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

// 默认头像（使用占位图）
const defaultAvatar = './assets/img/avatar-placeholder-1.png'

// 表单数据
const avatar = ref('')
const nickname = ref('轻松熊')
const gender = ref(2) // 1: 男, 2: 女
const phone = ref('')
const address = ref('广东省广州市海珠区 保利天悦小区A18栋203')
const agreed = ref(false)

// 保存
const handleSave = () => {
    if (!agreed.value) {
        uni.showToast({
            title: '请先同意用户隐私协议',
            icon: 'none'
        })
        return
    }
    uni.showToast({
        title: '保存成功',
        icon: 'success'
    })
}
</script>

<style lang="scss" scoped>
.my-profile {
    min-height: 100vh;
    background-color: #fff;
    padding: 0 28rpx;
}

/* 头像区域 */
.avatar-section {
    display: flex;
    justify-content: center;
    padding: 107rpx 0 60rpx;
}

.avatar-wrap {
    position: relative;
    width: 164rpx;
    height: 164rpx;
}

.avatar-inner {
    width: 164rpx;
    height: 164rpx;
    border-radius: 50%;
    overflow: hidden;
    background-color: #00B5B4;

    .avatar-img {
        width: 100%;
        height: 100%;
    }
}

.avatar-border {
    position: absolute;
    left: 0;
    top: 0;
    width: 164rpx;
    height: 164rpx;
    border-radius: 50%;
    border: 4rpx solid #F3FAFA;
    pointer-events: none;
    box-sizing: border-box;
}

.camera-btn {
    position: absolute;
    right: -4rpx;
    bottom: -4rpx;
    width: 44rpx;
    height: 44rpx;
    background: #F3FAFA;
    border-radius: 50%;
    border: 4rpx solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;

    .camera-icon {
        width: 24rpx;
        height: 20rpx;
    }
}

/* 表单区域 */
.form-section {
    padding: 0 8rpx;
}

.form-item {
    display: flex;
    align-items: center;
    padding: 36rpx 0;

    .label {
        font-size: 33rpx;
        font-weight: 500;
        color: #444;
        width: 145rpx;
        flex-shrink: 0;
    }

    .content {
        flex: 1;
        display: flex;
        align-items: center;
        padding-bottom: 36rpx;
        margin-left: 40rpx;
        border-bottom: 1rpx solid #F5F5F5;
    }

    .value {
        font-size: 33rpx;
        color: #222;
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

.gender-btns {
    display: flex;
    gap: 11rpx;
}

.gender-btn {
    padding: 0 32rpx;
    height: 51rpx;
    line-height: 51rpx;
    border-radius: 7rpx;
    background: #F6F7FB;
    color: #B9BECF;
    font-size: 33rpx;
    font-weight: 500;

    &.active {
        background: #00B6B4;
        color: #fff;
    }
}

.address-item {
    .content {
        align-items: flex-start;
        padding-top: 8rpx;
        padding-bottom: 28rpx;
    }
}

.address-value {
    flex: 1;
    font-size: 33rpx;
    color: #222;
    line-height: 1.4;
}

.arrow-icon {
    flex-shrink: 0;
}

/* 隐私协议 */
.agreement-section {
    display: flex;
    align-items: flex-start;
    padding: 80rpx 8rpx 0;
    gap: 20rpx;
}

.checkbox-wrap {
    padding-top: 6rpx;
}

.checkbox {
    width: 36rpx;
    height: 36rpx;
    border-radius: 50%;
    background: #00B6B4;
    display: flex;
    align-items: center;
    justify-content: center;

    &:not(.checked) {
        background: transparent;
        border: 2rpx solid #B9BECF;
    }
}

.agreement-text {
    flex: 1;
    font-size: 28rpx;
    line-height: 1.7;

    .text-gray {
        color: #B9BECF;
    }

    .text-link {
        color: #00A2A0;
    }
}

/* 保存按钮 */
.save-btn {
    margin-top: 22rpx;
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
