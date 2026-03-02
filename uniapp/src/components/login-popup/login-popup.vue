<template>
    <u-popup v-model="showPopup" mode="bottom" border-radius="29" :mask-close-able="false">
        <view class="login-popup">
            <!-- 标题 -->
            <text class="title">{{ title || '请完善您的资料' }}</text>

            <!-- 头像区域 -->
            <view class="avatar-wrapper">
                <view class="avatar-box" @click="handleAvatarClick">
                    <image
                        v-if="avatar"
                        class="avatar-image"
                        :src="avatar"
                        mode="aspectFill"
                    />
                    <view v-else class="avatar-placeholder">
                        <image class="logo-text" src="/static/images/login/icon_logo_text.png" mode="aspectFit"/>
                        <image class="logo-subtitle" src="/static/images/login/icon_logo_subtitle.png" mode="aspectFit"/>
                    </view>
                    <image class="edit-icon" src="/static/images/login/icon_edit.png" mode="aspectFit"/>
                </view>
            </view>

            <!-- 昵称输入 -->
            <view class="nickname-row">
                <text class="label">昵称</text>
                <input
                    class="nickname-input"
                    type="nickname"
                    v-model="nickname"
                    placeholder="请输入你的昵称"
                    @blur="handleNicknameBlur"
                />
            </view>

            <!-- 隐私协议 -->
            <view class="privacy-row">
                <view class="checkbox" :class="{ checked: agreed }" @click="agreed = !agreed">
                    <text v-if="agreed" class="checkbox-icon">✓</text>
                </view>
                <text class="privacy-text">
                    已阅读并且同意<text class="privacy-link" @click="handlePrivacyClick">《用户隐私协议》</text>，用户数据将用于个人信息展示与功能正常使用。
                </text>
            </view>

            <!-- 确定按钮 -->
            <view class="confirm-btn" @click="handleConfirm">
                <text class="confirm-text">确定</text>
            </view>

            <!-- 暂不登录 -->
            <text class="skip-text" @click="handleCancel">暂不登录</text>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: '请完善您的资料'
    },
    logo: {
        type: String,
        default: ''
    }
})

const emit = defineEmits<{
    (event: 'update:show', show: boolean): void
    (event: 'confirm', value: { avatar: string; nickname: string }): void
    (event: 'cancel'): void
}>()

const showPopup = computed({
    get() {
        return props.show
    },
    set(val) {
        emit('update:show', val)
    }
})

const avatar = ref('')
const nickname = ref('')
const agreed = ref(false)

// 头像点击 - 触发头像上传
const handleAvatarClick = () => {
    uni.chooseMedia({
        count: 1,
        mediaType: ['image'],
        sourceType: ['album', 'camera'],
        success: (res) => {
            const tempFilePath = res.tempFiles[0].tempFilePath
            // 这里可以调用上传接口，暂时直接使用本地路径
            avatar.value = tempFilePath
        }
    })
}

// 昵称输入失焦
const handleNicknameBlur = (e: any) => {
    nickname.value = e.detail.value
}

// 隐私协议点击
const handlePrivacyClick = () => {
    // 跳转到隐私协议页面
    uni.navigateTo({
        url: '/pages/privacy/privacy'
    })
}

// 确认提交
const handleConfirm = () => {
    if (!avatar.value) {
        uni.showToast({
            title: '请选择头像',
            icon: 'none'
        })
        return
    }
    if (!nickname.value) {
        uni.showToast({
            title: '请输入昵称',
            icon: 'none'
        })
        return
    }
    if (!agreed.value) {
        uni.showToast({
            title: '请阅读并同意隐私协议',
            icon: 'none'
        })
        return
    }

    emit('confirm', {
        avatar: avatar.value,
        nickname: nickname.value
    })
}

// 暂不登录
const handleCancel = () => {
    emit('cancel')
    showPopup.value = false
}
</script>

<style lang="scss" scoped>
.login-popup {
    padding: 58rpx 44rpx;
    background: #fff;
    border-radius: 29rpx 29rpx 0 0;
}

.title {
    display: block;
    text-align: center;
    font-size: 36rpx;
    font-weight: 500;
    color: #222929;
    margin-bottom: 22rpx;
}

.avatar-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 36rpx;
}

.avatar-box {
    position: relative;
    width: 164rpx;
    height: 164rpx;
    border-radius: 50%;
    background: #00B5B4;
    overflow: visible;
}

.avatar-image {
    width: 100%;
    height: 100%;
    border-radius: 50%;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    .logo-text {
        width: 118rpx;
        height: 38rpx;
    }

    .logo-subtitle {
        width: 118rpx;
        height: 18rpx;
        margin-top: 9rpx;
    }
}

.edit-icon {
    position: absolute;
    right: 0;
    bottom: 0;
    width: 44rpx;
    height: 44rpx;
    border-radius: 50%;
    background: #E4F7F7;
    border: 4rpx solid #fff;
}

.nickname-row {
    display: flex;
    align-items: center;
    height: 80rpx;
    border-bottom: 1rpx solid #E5E5E5;
    margin-bottom: 44rpx;

    .label {
        font-size: 29rpx;
        color: #222929;
        width: 60rpx;
        flex-shrink: 0;
    }

    .nickname-input {
        flex: 1;
        font-size: 29rpx;
        color: #222929;
    }
}

.privacy-row {
    display: flex;
    align-items: flex-start;
    margin-bottom: 38rpx;

    .checkbox {
        width: 29rpx;
        height: 29rpx;
        border: 2rpx solid #00B6B4;
        border-radius: 4rpx;
        margin-right: 13rpx;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 4rpx;

        &.checked {
            background: #00B6B4;

            .checkbox-icon {
                color: #fff;
                font-size: 20rpx;
            }
        }
    }

    .privacy-text {
        font-size: 25rpx;
        color: #9CA6A6;
        line-height: 44rpx;
    }

    .privacy-link {
        color: #00A2A0;
    }
}

.confirm-btn {
    height: 102rpx;
    background: #00B6B4;
    border-radius: 51rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 40rpx;

    .confirm-text {
        font-size: 36rpx;
        font-weight: 500;
        color: #fff;
    }
}

.skip-text {
    display: block;
    text-align: center;
    font-size: 28rpx;
    color: #9CA6A6;
}
</style>