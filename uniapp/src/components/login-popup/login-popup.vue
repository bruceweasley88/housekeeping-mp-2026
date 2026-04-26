<template>
    <u-popup v-model="showPopup" mode="bottom" border-radius="29" :mask-close-able="false">
        <view class="login-popup">
            <!-- 加载中状态 -->
            <template v-if="loginState === 'loading'">
                <view class="loading-state">
                    <u-loading mode="circle" size="80"></u-loading>
                    <text class="loading-text">{{ isNewUser ? '正在登录...' : '社区邻居生活互助平台' }}</text>
                </view>
            </template>

            <!-- 表单状态 -->
            <template v-else>
                <!-- 标题 -->
                <text class="title">{{ title || '请完善您的资料' }}</text>

                <!-- 头像区域 -->
                <view class="avatar-wrapper">
                    <avatar-upload
                        v-model="avatarUrl"
                        :size="164"
                        round
                        @update:modelValue="handleAvatarChange"
                    />
                </view>

                <!-- 昵称输入 -->
                <view class="nickname-row">
                    <text class="label">昵称</text>
                    <input
                        class="nickname-input"
                        type="nickname"
                        :value="nickname"
                        placeholder="请输入你的昵称"
                        @input="handleNicknameInput"
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
                <view
                    class="confirm-btn"
                    :class="{ disabled: loginState === 'submitting' }"
                    @click="handleConfirm"
                >
                    <text class="confirm-text">{{ loginState === 'submitting' ? '提交中...' : '确定' }}</text>
                </view>

                <!-- 暂不登录 -->
                <text class="skip-text" @click="handleCancel">暂不登录</text>
            </template>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, watch } from 'vue'
import { mnpLogin, updateUser } from '@/api/account'
import { useUserStore } from '@/stores/user'

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
    (event: 'success', data: { token: string }): void
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

const userStore = useUserStore()
const temToken = ref('')
const avatarUrl = ref('')
const nickname = ref('')
const agreed = ref(false)

// 登录状态：'loading' | 'form' | 'submitting'
const loginState = ref<'loading' | 'form' | 'submitting'>('loading')
// 是否是新用户
const isNewUser = ref(true)

// 组件挂载时自动获取微信登录凭证
onMounted(async () => {
    if (!props.show) return
    await initLogin()
})

// 监听 show 变化，显示时初始化登录
watch(() => props.show, async (val) => {
    if (val && !temToken.value) {
        await initLogin()
    }
})

// 初始化登录，获取临时 token
const initLogin = async () => {
    try {
        loginState.value = 'loading'
        const loginRes = await uni.login({ provider: 'weixin' })
        console.log('uni.login 返回:', loginRes)
        const { code } = loginRes
        if (!code) {
            console.error('获取 code 失败:', loginRes)
            uni.$u.toast('获取微信登录凭证失败')
            loginState.value = 'form'
            return
        }
        const data = await mnpLogin({ code })
        console.log('mnpLogin 返回:', data)
        temToken.value = data.token
        // 设置到 store，供 avatar-upload 组件使用
        userStore.temToken = data.token

        // 判断是否是新用户（is_new_user: 0 表示已注册，1 表示新用户）
        isNewUser.value = data.is_new_user !== 0

        if (!isNewUser.value) {
            // 已注册用户，延迟后直接登录
            setTimeout(() => {
                userStore.login(temToken.value)
                emit('success', { token: temToken.value })
                showPopup.value = false
                // 通知首页刷新数据
                uni.$emit('loginSuccess')
            }, 1500)
        } else {
            // 新用户，显示表单
            loginState.value = 'form'
        }
    } catch (error: any) {
        console.error('initLogin 错误:', error)
        loginState.value = 'form'  // 出错时也显示表单
        uni.$u.toast(error?.message || error || '登录初始化失败，请重试')
    }
}

// 头像上传成功回调
const handleAvatarChange = (url: string) => {
    avatarUrl.value = url
}

// 昵称输入
const handleNicknameInput = (e: any) => {
    nickname.value = e.detail.value
}

// 隐私协议点击
const handlePrivacyClick = () => {
    uni.navigateTo({
        url: '/pages/agreement/agreement?type=privacy'
    })
}

// 确认提交
const handleConfirm = async () => {
    if (!nickname.value.trim()) {
        uni.$u.toast('请输入昵称')
        return
    }
    if (!agreed.value) {
        uni.$u.toast('请阅读并同意隐私协议')
        return
    }
    if (!temToken.value) {
        uni.$u.toast('登录状态异常，请重试')
        return
    }

    loginState.value = 'submitting'
    try {
        await updateUser({
            nickname: nickname.value.trim(),
            avatar: avatarUrl.value
        }, { token: temToken.value })

        // 正式登录
        userStore.login(temToken.value)
        emit('success', { token: temToken.value })
        showPopup.value = false
        // 通知首页刷新数据
        uni.$emit('loginSuccess')
    } catch (error: any) {
        uni.$u.toast(error || '提交失败')
    } finally {
        loginState.value = 'form'
    }
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

.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 100rpx 0;
}

.loading-text {
    font-size: 28rpx;
    color: #666;
    margin-top: 30rpx;
}

.avatar-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 36rpx;
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
        margin-right: 20rpx;
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

    &.disabled {
        opacity: 0.6;
    }

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
