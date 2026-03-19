<template>
    <view class="upload-bankcard">
        <!-- 状态提示 -->
        <view v-if="status !== null" class="status-banner" :class="statusClass">
            <text class="status-text">{{ statusText }}</text>
            <text v-if="status === 2 && rejectReason" class="reject-reason">{{ rejectReason }}</text>
        </view>

        <!-- 您的姓名 表单项 -->
        <view class="form-section">
            <text class="form-label">您的姓名</text>
            <view class="form-item flex justify-between items-center">
                <input
                    class="form-input"
                    v-model="realName"
                    placeholder="请输入真实姓名"
                    :disabled="isDisabled"
                    placeholder-class="form-placeholder"
                />
            </view>
        </view>

        <!-- 上传卡照片 标题 -->
        <text class="section-title">上传卡照片</text>

        <!-- 银行卡图片区域 -->
        <view class="bankcard-wrapper" @click="handleCapture">
            <image
                class="bankcard-image"
                :src="bankcardPreview"
                mode="widthFix"
            />
            <!-- 拍摄按钮（覆盖在图片底部） -->
            <view v-if="!isDisabled" class="capture-btn">
                <text class="capture-btn-text">拍摄银行卡正面</text>
            </view>
        </view>

        <!-- 说明文字 -->
        <text class="description">
            说明：请拍照上传银行卡正面，人工审核成功后，您的银行卡信息将显示为"已上传"，提现将会打款到您上传的银行卡，请注意银行卡拍照清晰。
        </text>

        <!-- 确认上传按钮 -->
        <view
            class="submit-btn"
            :class="{ 'submit-btn-disabled': isDisabled }"
            @click="handleSubmit"
        >
            <text class="submit-btn-text">{{ btnText }}</text>
        </view>

        <!-- 审批中提示 -->
        <text v-if="status === 0" class="pending-tip">您的申请正在审核中，请耐心等待</text>
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { submitUserBankcard, getUserBankcardDetail } from '@/api/userBankcard'
import { uploadImage } from '@/api/app'

// 状态: null=未提交, 0=待审核, 1=已通过, 2=已拒绝
const status = ref<number | null>(null)
const loading = ref(true)

// 表单数据
const realName = ref('')
const bankcardImage = ref('')
const rejectReason = ref('')

// 预览图片
const bankcardPreview = computed(() => {
    return bankcardImage.value || '/packages/static/images/img_bankcard.png'
})

// 状态文字
const statusText = computed(() => {
    if (status.value === 0) return '审批中'
    if (status.value === 1) return '已通过'
    if (status.value === 2) return '已拒绝'
    return ''
})

// 状态样式类
const statusClass = computed(() => {
    if (status.value === 0) return 'status-pending'
    if (status.value === 1) return 'status-pass'
    if (status.value === 2) return 'status-reject'
    return ''
})

// 按钮文字
const btnText = computed(() => {
    if (status.value === 0) return '审批中'
    if (status.value === 1) return '已认证'
    if (status.value === 2) return '重新提交'
    return '确认上传'
})

// 是否禁用操作
const isDisabled = computed(() => {
    return status.value === 0 || status.value === 1
})

// 获取银行卡详情
const fetchDetail = async () => {
    loading.value = true
    try {
        const res = await getUserBankcardDetail()
        if (res && res.id) {
            status.value = res.status
            rejectReason.value = res.reject_reason || ''
            // 已拒绝时清空表单，允许重新填写
            if (res.status === 2) {
                realName.value = ''
                bankcardImage.value = ''
            } else {
                // 待审核或已通过时，回显数据
                realName.value = res.real_name || ''
                bankcardImage.value = res.bankcard_image || ''
            }
        } else {
            status.value = null
            realName.value = ''
            bankcardImage.value = ''
            rejectReason.value = ''
        }
    } catch (error) {
        console.error('获取银行卡详情失败:', error)
    } finally {
        loading.value = false
    }
}

// 上传图片
const uploadImageFile = async (filePath: string): Promise<string> => {
    uni.showLoading({ title: '上传中...' })
    try {
        const res = await uploadImage(filePath)
        uni.hideLoading()
        return res.uri
    } catch (error) {
        uni.hideLoading()
        uni.$u.toast('上传失败')
        throw error
    }
}

// 拍摄银行卡
const handleCapture = () => {
    if (isDisabled.value) return
    uni.chooseImage({
        count: 1,
        sourceType: ['camera', 'album'],
        success: async (res) => {
            const uri = await uploadImageFile(res.tempFilePaths[0])
            bankcardImage.value = uri
        }
    })
}

// 提交银行卡
const handleSubmit = async () => {
    if (isDisabled.value) return

    // 表单验证
    if (!realName.value.trim()) {
        uni.$u.toast('请填写真实姓名')
        return
    }
    if (!bankcardImage.value) {
        uni.$u.toast('请上传银行卡照片')
        return
    }

    uni.showLoading({ title: '提交中...' })
    try {
        await submitUserBankcard({
            real_name: realName.value.trim(),
            bankcard_image: bankcardImage.value
        })
        uni.hideLoading()
        uni.$u.toast('提交成功')
        // 更新状态为待审核
        status.value = 0
    } catch (error: any) {
        uni.hideLoading()
        uni.$u.toast(error.msg || '提交失败')
    }
}

onLoad(() => {
    fetchDetail()
})
</script>

<style lang="scss" scoped>
.upload-bankcard {
    min-height: 100vh;
    background-color: #f6f6f6;
    padding: 0 30rpx;
    padding-bottom: 60rpx;
}

.status-banner {
    margin: 20rpx 0;
    padding: 20rpx 30rpx;
    border-radius: 15rpx;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.status-pending {
    background-color: #fff3e0;
}

.status-pass {
    background-color: #e8f5e9;
}

.status-reject {
    background-color: #ffebee;
}

.status-text {
    font-size: 28rpx;
    font-weight: 500;
}

.status-pending .status-text {
    color: #ff7e22;
}

.status-pass .status-text {
    color: #00a2a0;
}

.status-reject .status-text {
    color: #f04530;
}

.reject-reason {
    font-size: 24rpx;
    color: #f04530;
    margin-top: 10rpx;
}

.form-section {
    margin-top: 24rpx;
}

.form-label {
    display: block;
    font-size: 29rpx;
    font-weight: 500;
    color: #222222;
    margin-bottom: 22rpx;
}

.form-item {
    background-color: #ffffff;
    border-radius: 22rpx;
    height: 104rpx;
    padding: 0 29rpx;
}

.form-input {
    flex: 1;
    font-size: 33rpx;
    color: #222222;
}

.form-placeholder {
    font-size: 33rpx;
    color: #999999;
}

.section-title {
    display: block;
    font-size: 29rpx;
    font-weight: 500;
    color: #222222;
    margin-top: 40rpx;
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
    margin-top: 60rpx;
    background-color: #00b6b4;
    border-radius: 51rpx;
    height: 102rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.submit-btn-disabled {
    background-color: #dadada;
}

.submit-btn-text {
    font-size: 36rpx;
    font-weight: 500;
    color: #ffffff;
}

.pending-tip {
    display: block;
    text-align: center;
    font-size: 24rpx;
    color: #ff7e22;
    margin-top: 20rpx;
}
</style>
