<template>
    <view class="my-profile">
        <!-- 头像区域 -->
        <view class="avatar-section">
            <view class="avatar-wrap">
                <avatar-upload
                    v-model="avatar"
                    file-key="url"
                    :round="true"
                    :size="164"
                />
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
                    <input
                        class="input"
                        v-model="nickname"
                        type="text"
                        placeholder="填写您的昵称"
                        placeholder-class="placeholder-class"
                    />
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
            <view class="form-item address-item" @click="handleSelectCommunity">
                <text class="label">你的地址</text>
                <view class="content">
                    <text class="address-value" :class="{ 'placeholder': !addressText }">
                        {{ addressText || '请选择您的地址' }}
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
import { ref, onMounted } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import { getUserInfo, userEdit } from '@/api/user'
import { getUserAddress } from '@/api/community'
import { useUserStore } from '@/stores/user'

// 默认头像（使用占位图）
const defaultAvatar = './assets/img/avatar-placeholder-1.png'

// 表单数据
const avatar = ref('')
const nickname = ref('')
const gender = ref(1) // 1: 男, 2: 女
const phone = ref('')
const agreed = ref(true) // 默认勾选隐私协议
const addressText = ref('')

const userStore = useUserStore()

// 获取用户信息
const getUser = async () => {
    const data = await getUserInfo()
    avatar.value = data.avatar || ''
    nickname.value = data.nickname || ''
    // 后端返回的是中文，需要转换为数字
    gender.value = data.sex === '女' ? 2 : (data.sex === '男' ? 1 : 0)
    phone.value = data.mobile || ''
}

// 获取用户地址
const fetchUserAddress = async () => {
    try {
        const res = await getUserAddress()
        if (res && res.community_name) {
            // 显示小区名 + 楼号 + 门牌号
            const parts = [res.community_name]
            if (res.building) parts.push(res.building)
            if (res.room) parts.push(res.room)
            addressText.value = parts.join(' ')
        }
    } catch (e) {
        // 未登录或无地址信息
        addressText.value = ''
    }
}

// 跳转选择小区页面
const handleSelectCommunity = () => {
    uni.navigateTo({
        url: '/pages/select-community/select-community'
    })
}

onMounted(() => {
    getUser()
})

// 每次显示页面时刷新地址
onShow(() => {
    fetchUserAddress()
})

// 保存
const handleSave = async () => {
    if (!agreed.value) {
        uni.showToast({
            title: '请先同意用户隐私协议',
            icon: 'none'
        })
        return
    }

    uni.showLoading({ title: '保存中...' })

    try {
        // 保存头像
        if (avatar.value) {
            await userEdit({ field: 'avatar', value: avatar.value })
        }
        // 保存昵称
        if (nickname.value) {
            await userEdit({ field: 'nickname', value: nickname.value })
        }
        // 保存性别
        await userEdit({ field: 'sex', value: gender.value })
        // 保存手机号
        if (phone.value) {
            await userEdit({ field: 'mobile', value: phone.value })
        }

        // 刷新用户 store
        await userStore.getUser()

        uni.hideLoading()

        // 返回上一页
        uni.navigateBack()
    } catch (error) {
        uni.hideLoading()
    }
}

onMounted(() => {
    getUser()
})
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

    :deep(.avatar-upload) {
        border-radius: 50% !important;
        overflow: hidden;
        background-color: #00B5B4 !important;
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
    pointer-events: none;

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
    padding: 36rpx 0 16rpx;

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
        margin-left: 40rpx;
        padding-bottom: 20rpx;
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
    }
}

.address-value {
    flex: 1;
    font-size: 33rpx;
    color: #222;
    line-height: 1.4;

    &.placeholder {
        color: #CDCDCD;
    }
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
