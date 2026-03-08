<template>
    <view class="page">
        <!-- 表单区域 -->
        <view class="form-section">
            <!-- 您的小区 -->
            <view class="form-item" @click="handleSelectCommunity">
                <view class="form-label">您的小区</view>
                <view class="form-content">
                    <text class="form-placeholder" :class="{ 'has-value': community }">{{ community || '请选择所在地址' }}</text>
                    <view class="arrow-icon"></view>
                </view>
            </view>

            <!-- 小区楼号 -->
            <view class="form-item">
                <view class="form-label">小区楼号</view>
                <view class="form-content">
                    <input
                        class="form-input"
                        v-model="building"
                        placeholder="例：A15"
                        placeholder-style="color: #DADADA; font-size: 32rpx;"
                    />
                </view>
            </view>

            <!-- 层数门牌 -->
            <view class="form-item">
                <view class="form-label">层数门牌</view>
                <view class="form-content">
                    <input
                        class="form-input"
                        v-model="room"
                        placeholder="例：1206室"
                        placeholder-style="color: #DADADA; font-size: 32rpx;"
                    />
                </view>
            </view>
        </view>

        <!-- 提示文字 -->
        <view class="tip-wrapper">
            <text class="tip-text">填写小区后，才能进入首页看到相关内容</text>
        </view>

        <!-- 保存按钮 -->
        <view class="save-btn" :class="{ 'disabled': saving }" @click="handleSave">
            <text class="save-text">{{ saving ? '保存中...' : '保存地址' }}</text>
        </view>
    </view>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { getUserAddress, saveUserAddress } from '@/api/community'

// 小区信息
const communityId = ref(0)
const community = ref('')
const building = ref('')
const room = ref('')

// 保存状态
const saving = ref(false)

// 监听小区选择事件
onMounted(async () => {
    // 监听小区选择
    uni.$on('community-selected', (data: { id: number; name: string; address: string }) => {
        communityId.value = data.id
        community.value = data.name
    })

    // 获取已保存的地址
    try {
        const res = await getUserAddress()
        if (res && res.community_id) {
            communityId.value = res.community_id
            community.value = res.community_name || ''
            building.value = res.building || ''
            room.value = res.room || ''
        }
    } catch (e) {
        // 未登录或无地址信息，忽略错误
    }
})

// 移除事件监听
onUnmounted(() => {
    uni.$off('community-selected')
})

const handleSelectCommunity = () => {
    uni.navigateTo({
        url: '/pages/community-list/community-list'
    })
}

const handleSave = async () => {
    if (saving.value) return

    if (!communityId.value) {
        uni.showToast({
            title: '请选择小区',
            icon: 'none'
        })
        return
    }
    if (!building.value) {
        uni.showToast({
            title: '请输入楼号',
            icon: 'none'
        })
        return
    }
    if (!room.value) {
        uni.showToast({
            title: '请输入门牌号',
            icon: 'none'
        })
        return
    }

    saving.value = true
    try {
        await saveUserAddress({
            community_id: communityId.value,
            building: building.value,
            room: room.value
        })

        uni.showToast({
            title: '保存成功',
            icon: 'success'
        })

        // 返回首页
        setTimeout(() => {
            uni.switchTab({
                url: '/pages/index/index'
            })
        }, 1500)
    } catch (e: any) {
        uni.showToast({
            title: e.message || '保存失败',
            icon: 'none'
        })
    } finally {
        saving.value = false
    }
}
</script>

<style lang="scss" scoped>
.page {
    min-height: 100vh;
    background: #fff;
    display: flex;
    flex-direction: column;
}

// 表单区域
.form-section {
    padding: 0 36rpx;
    margin-top: 36rpx;
}

.form-item {
    display: flex;
    align-items: center;
    height: 100rpx;
    border-bottom: 2rpx solid #F5F5F5;
}

.form-label {
    font-size: 32rpx;
    font-weight: 500;
    color: #616B6B;
    width: 140rpx;
    flex-shrink: 0;
}

.form-content {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 100%;
}

.form-placeholder {
    font-size: 32rpx;
    color: #DADADA;
}

.form-placeholder.has-value {
    color: #222929;
}

.form-input {
    flex: 1;
    font-size: 32rpx;
    color: #222929;
    height: 100%;
}

.arrow-icon {
    width: 16rpx;
    height: 16rpx;
    border-right: 4rpx solid #DADADA;
    border-bottom: 4rpx solid #DADADA;
    transform: rotate(-45deg);
    flex-shrink: 0;
    margin-left: 16rpx;
}

// 提示文字
.tip-wrapper {
    margin-top: auto;
    padding: 30rpx 40rpx;
    display: flex;
    justify-content: center;
}

.tip-text {
    font-size: 26rpx;
    color: #9CA6A6;
    text-align: center;
}

// 保存按钮
.save-btn {
    margin: 40rpx 30rpx calc(40rpx + env(safe-area-inset-bottom));
    height: 100rpx;
    background: #00B6B4;
    border-radius: 50rpx;
    display: flex;
    align-items: center;
    justify-content: center;

    &.disabled {
        opacity: 0.6;
    }
}

.save-text {
    font-size: 36rpx;
    font-weight: 500;
    color: #fff;
}
</style>
