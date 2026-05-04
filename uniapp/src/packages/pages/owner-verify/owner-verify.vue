<template>
    <view class="owner-verify">
        <!-- 状态提示 -->
        <view v-if="status !== null" class="status-banner" :class="statusClass">
            <text class="status-text">{{ statusText }}</text>
        </view>

        <!-- 小区选择 -->
        <view class="community-section">
            <text class="section-title">认证小区</text>
            <view v-if="status === null || status === 2" class="community-picker" @click="handleSelectCommunity">
                <text :class="communityName ? 'community-name' : 'community-placeholder'">
                    {{ communityName || '请选择要认证的小区' }}
                </text>
                <text class="picker-arrow">></text>
            </view>
            <view v-else class="community-readonly">
                <text class="community-name">{{ communityName || '未知小区' }}</text>
            </view>
        </view>

        <!-- 实名认证 标题 -->
        <text class="section-title">实名认证</text>

        <!-- 身份证正面 -->
        <view class="idcard-wrapper" @click="handleCaptureFront">
            <image
                class="idcard-image"
                :src="idcardFrontPreview"
                mode="widthFix"
            />
        </view>

        <!-- 身份证反面 -->
        <view class="idcard-wrapper" @click="handleCaptureBack">
            <image
                class="idcard-image"
                :src="idcardBackPreview"
                mode="widthFix"
            />
        </view>

        <!-- 业主和住户认证 标题区 -->
        <view class="section-header flex justify-between items-center">
            <text class="section-title">业主和住户认证</text>
            <text class="section-subtitle">水电费缴费截图、租房合同等</text>
        </view>

        <!-- 已上传的材料预览 -->
        <image-upload
            v-model="materials"
            :max-count="9"
            :disabled="status === 0 || status === 1"
        />

        <!-- 说明文字 -->
        <text class="description">业主认证通过后，才能在该小区发布和承接需求</text>

        <!-- 立即认证按钮 -->
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
import { onLoad, onShow } from '@dcloudio/uni-app'
import { submitUserVerify, getUserVerifyDetail } from '@/api/userVerify'
import { getUserAddress } from '@/api/community'
import { uploadImage } from '@/api/app'
import ImageUpload from '@/components/image-upload/image-upload.vue'

// 状态: null=未提交, 0=待审核, 1=已通过, 2=已拒绝
const status = ref<number | null>(null)
const loading = ref(true)

// 小区相关
const communityId = ref(0)
const communityName = ref('')

// 图片数据
const idcardFront = ref('')
const idcardBack = ref('')
const materials = ref<string[]>([])

// 预览图片
const idcardFrontPreview = computed(() => {
    return idcardFront.value || '/packages/static/images/img_photoidz.png'
})

const idcardBackPreview = computed(() => {
    return idcardBack.value || '/packages/static/images/img_photoidf.png'
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
    return '立即认证'
})

// 是否禁用按钮
const isDisabled = computed(() => {
    return status.value === 0 || status.value === 1
})

// 获取认证状态
const fetchDetail = async () => {
    loading.value = true
    try {
        // 先获取用户当前小区
        const addressRes = await getUserAddress()
        if (addressRes && addressRes.community_id) {
            communityId.value = addressRes.community_id
            communityName.value = addressRes.community_name || ''
        }

        // 查询该小区的认证状态
        const res = await getUserVerifyDetail({ community_id: communityId.value || undefined })
        if (res && res.id) {
            status.value = res.status
            communityName.value = res.community_name || communityName.value
            communityId.value = res.community_id || communityId.value
            // 已拒绝时清空表单，允许重新填写
            if (res.status === 2) {
                idcardFront.value = ''
                idcardBack.value = ''
                materials.value = []
            } else {
                // 待审核或已通过时，回显图片
                idcardFront.value = res.idcard_front || ''
                idcardBack.value = res.idcard_back || ''
                materials.value = res.verify_materials || []
            }
        } else {
            status.value = null
        }
    } catch (error) {
        console.error('获取认证状态失败:', error)
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

// 选择小区
const handleSelectCommunity = () => {
    uni.navigateTo({
        url: '/pages/select-community/select-community?mode=verify'
    })
}

// 拍摄身份证正面
const handleCaptureFront = () => {
    if (status.value === 0 || status.value === 1) {
        return
    }
    uni.chooseImage({
        count: 1,
        sourceType: ['camera', 'album'],
        success: async (res) => {
            const uri = await uploadImageFile(res.tempFilePaths[0])
            idcardFront.value = uri
        }
    })
}

// 拍摄身份证反面
const handleCaptureBack = () => {
    if (status.value === 0 || status.value === 1) {
        return
    }
    uni.chooseImage({
        count: 1,
        sourceType: ['camera', 'album'],
        success: async (res) => {
            const uri = await uploadImageFile(res.tempFilePaths[0])
            idcardBack.value = uri
        }
    })
}

// 提交认证
const handleSubmit = async () => {
    if (isDisabled.value) return

    // 表单验证
    if (!communityId.value) {
        uni.$u.toast('请选择要认证的小区')
        return
    }
    if (!idcardFront.value) {
        uni.$u.toast('请上传身份证正面')
        return
    }
    if (!idcardBack.value) {
        uni.$u.toast('请上传身份证反面')
        return
    }
    if (materials.value.length === 0) {
        uni.$u.toast('请上传至少一张证明材料')
        return
    }

    uni.showLoading({ title: '提交中...' })
    try {
        await submitUserVerify({
            community_id: communityId.value,
            idcard_front: idcardFront.value,
            idcard_back: idcardBack.value,
            verify_materials: materials.value
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

// 每次页面显示时刷新（从小区选择页返回时）
onShow(() => {
    if (communityId.value > 0) return // 已有小区则不重复加载
    // 尝试获取用户地址来填充小区
    getUserAddress().then((res) => {
        if (res && res.community_id) {
            communityId.value = res.community_id
            communityName.value = res.community_name || ''
        }
    }).catch(() => {})
})
</script>

<style lang="scss" scoped>
.owner-verify {
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

.community-section {
    margin: 20rpx 0;
}

.community-picker {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #ffffff;
    border-radius: 15rpx;
    padding: 24rpx 30rpx;
}

.community-name {
    font-size: 28rpx;
    color: #222929;
    font-weight: 500;
}

.community-placeholder {
    font-size: 28rpx;
    color: #DADADA;
}

.picker-arrow {
    font-size: 28rpx;
    color: #9CA6A6;
}

.community-readonly {
    background-color: #ffffff;
    border-radius: 15rpx;
    padding: 24rpx 30rpx;
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

.description {
    display: block;
    font-size: 25rpx;
    color: #666666;
    margin-top: 80rpx;
    text-align: center;
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
