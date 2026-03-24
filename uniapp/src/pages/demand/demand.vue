<template>
    <view class="page">
        <!-- 加载状态 -->
        <view v-if="loading" class="loading-wrap">
            <u-loading mode="circle" size="60"></u-loading>
            <text class="loading-text">加载中...</text>
        </view>

        <!-- 内容区域 -->
        <template v-else-if="demandData">
            <!-- 流程说明卡片 - 仅陌生人承接时显示 -->
            <view v-if="showFlowCard" class="flow-card">
                <image class="flow-icon" src="/static/images/img_taskwz.png" mode="aspectFit" />
                <view class="flow-steps">
                    <text class="flow-text">需求发布</text>
                    <text class="flow-arrow">›</text>
                    <text class="flow-text">承接需求</text>
                    <text class="flow-arrow">›</text>
                    <text class="flow-text">完成需求</text>
                    <text class="flow-arrow">›</text>
                    <text class="flow-text">平台结算</text>
                </view>
            </view>

            <!-- 状态卡片 -->
            <view v-if="showStatusCard" class="status-card">
                <view class="status-icon">
                    <view class="icon-circle">
                        <view class="check-mark"></view>
                    </view>
                </view>
                <view class="status-info">
                    <text class="status-title">{{ statusCardInfo.title }}</text>
                    <text class="status-desc">{{ statusCardInfo.desc }}</text>
                </view>
            </view>

            <!-- 需求信息卡片 -->
            <view class="demand-card">
                <text class="demand-title">{{ demandData.title }}</text>
                <view class="demand-meta">
                    <view class="type-tag">
                        <text class="type-text">{{ demandData.category?.name || '' }}</text>
                    </view>
                    <text class="price-symbol">¥</text>
                    <text class="price-value">{{ priceDisplay.value }}</text>
                    <text class="price-unit">{{ priceDisplay.unit }}</text>
                    <text v-if="priceDisplay.extra" class="price-hours">{{ priceDisplay.extra }}</text>
                </view>
            </view>

            <!-- 需求内容区块 -->
            <view class="content-card">
                <view class="section-header">
                    <view class="dot-indicator"></view>
                    <text class="section-title">需求内容</text>
                </view>
                <view class="detail-row">
                    <text class="detail-label">需求详情</text>
                    <text class="detail-text">{{ demandData.description }}</text>
                </view>
                <view v-if="showPhotoSection" class="photo-row">
                    <text class="detail-label">照片信息</text>
                    <scroll-view scroll-x class="photo-list">
                        <image
                            v-for="(img, idx) in demandData.images"
                            :key="idx"
                            class="photo-img"
                            :src="img"
                            mode="aspectFill"
                            @click="previewImage(idx)"
                        />
                    </scroll-view>
                </view>
            </view>

            <!-- 发布人信息区块 -->
            <view v-if="showPublisherCard" class="publisher-card">
                <view class="section-header">
                    <view class="dot-indicator"></view>
                    <text class="section-title">发布人信息</text>
                </view>
                <view class="publisher-info">
                    <view class="publisher-row">
                        <image class="avatar" :src="demandData.user_info?.avatar || defaultAvatar" mode="aspectFill" />
                        <view class="publisher-text">
                            <text class="publisher-name">{{ demandData.user_info?.nickname || '' }}</text>
                            <text class="publisher-time">{{ formatTime(demandData.create_time, '发布于') }}</text>
                        </view>
                        <view v-if="userRole !== 'owner'" class="contact-btn" @click="handleContact">
                            <text class="contact-text">立即沟通</text>
                        </view>
                    </view>
                </view>
                <view class="info-row">
                    <text class="info-label">联系电话</text>
                    <text class="info-value">{{ displayPhone }}</text>
                </view>
                <view class="info-row">
                    <text class="info-label">小区地址</text>
                    <text class="info-value">{{ fullAddress }}</text>
                </view>
            </view>

            <!-- 承接人信息区块 -->
            <view v-if="showAccepterCard" class="receiver-card">
                <view class="section-header">
                    <view class="dot-indicator"></view>
                    <text class="section-title">承接人信息</text>
                </view>
                <view class="receiver-info">
                    <view class="receiver-row">
                        <image class="avatar" :src="demandData.accept_info?.avatar || defaultAvatar" mode="aspectFill" />
                        <view class="receiver-text">
                            <text class="receiver-name">{{ demandData.accept_info?.nickname || '' }}</text>
                            <text class="receiver-time">{{ formatTime(demandData.accept_info?.accept_time, '承接于') }}</text>
                        </view>
                        <view class="contact-btn" @click="handleContact">
                            <text class="contact-text">立即沟通</text>
                        </view>
                    </view>
                </view>
            </view>

            <!-- 底部占位 -->
            <view v-if="hasBottomBar" class="bottom-placeholder"></view>
        </template>

        <!-- 底部操作栏 -->
        <view v-if="hasBottomBar" class="bottom-bar">
            <template v-for="(btn, idx) in bottomButtons" :key="idx">
                <!-- 分享按钮使用 button open-type -->
                <button
                    v-if="btn.action === 'share'"
                    class="primary-btn share-btn"
                    open-type="share"
                >
                    <text class="primary-text">{{ btn.text }}</text>
                </button>
                <view
                    v-else-if="btn.type === 'secondary'"
                    class="secondary-btn"
                    @click="handleButtonClick(btn.action)"
                >
                    <text class="secondary-text">{{ btn.text }}</text>
                </view>
                <view
                    v-else-if="btn.type === 'primary'"
                    class="primary-btn"
                    @click="handleButtonClick(btn.action)"
                >
                    <text class="primary-text">{{ btn.text }}</text>
                </view>
                <view v-else class="disabled-btn">
                    <text class="disabled-text">{{ btn.text }}</text>
                </view>
            </template>
        </view>

        <!-- 调整金额弹窗 -->
        <u-popup v-model="showAdjustPopup" mode="bottom" border-radius="29" :mask-close-able="false">
            <view class="adjust-popup">
                <view class="popup-header">
                    <text class="popup-title">调整金额</text>
                    <view class="close-btn" @click="showAdjustPopup = false">
                        <text class="close-icon">×</text>
                    </view>
                </view>
                <view class="popup-content">
                    <view class="current-row">
                        <text class="popup-label">当前金额</text>
                        <text class="current-amount">¥{{ demandData?.amount || 0 }}</text>
                    </view>
                    <text class="popup-label">调整后金额</text>
                    <view class="amount-input-row">
                        <text class="amount-symbol">¥</text>
                        <input
                            class="amount-input"
                            type="digit"
                            v-model="adjustAmount"
                            placeholder="请输入金额"
                            placeholder-style="color: #DADADA;"
                        />
                    </view>
                </view>
                <view class="popup-buttons">
                    <view class="btn-cancel" @click="showAdjustPopup = false">
                        <text class="btn-cancel-text">取消</text>
                    </view>
                    <view class="btn-confirm" @click="handleConfirmAdjust">
                        <text class="btn-confirm-text">确定</text>
                    </view>
                </view>
            </view>
        </u-popup>

        <!-- 确认弹窗 -->
        <u-modal
            v-model="showConfirmModal"
            :title="confirmModalTitle"
            :content="confirmModalContent"
            :show-cancel-button="true"
            @confirm="handleConfirmAction"
            @cancel="showConfirmModal = false"
        />
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { onLoad, onShow, onShareAppMessage } from '@dcloudio/uni-app'
import {
    getDemandDetail,
    acceptDemand,
    cancelAcceptDemand,
    finishDemand,
    adjustDemandAmount
} from '@/api/demand'
import { settleDemand } from '@/api/bill'
import { createChatSession } from '@/api/chat'

const defaultAvatar = '/static/images/publish/icon_successb.png'

// ============ 响应式数据 ============
const demandId = ref(0)
const demandData = ref<any>(null)
const loading = ref(true)
const submitting = ref(false)

// 弹窗状态
const showAdjustPopup = ref(false)
const adjustAmount = ref('')
const showConfirmModal = ref(false)
const confirmActionType = ref<'accept' | 'cancelAccept' | 'finish' | null>(null)

// ============ 计算属性：角色与状态 ============
const userRole = computed(() => {
    if (!demandData.value) return 'stranger'
    if (demandData.value.is_owner) return 'owner'
    if (demandData.value.is_accepter) return 'accepter'
    return 'stranger'
})

const demandStatus = computed(() => demandData.value?.status || 0)

// ============ 计算属性：显示逻辑 ============
const showStatusCard = computed(() => {
    const role = userRole.value
    const status = demandStatus.value
    if (role === 'owner') return true
    if (role === 'accepter') return [2, 3, 6].includes(status)
    return false
})

// 流程卡片显示条件：陌生人查看待承接需求
const showFlowCard = computed(() => {
    return userRole.value === 'stranger' && demandStatus.value === 1
})

const statusCardInfo = computed(() => {
    const role = userRole.value
    const status = demandStatus.value

    const ownerTexts: Record<number, { title: string; desc: string }> = {
        1: { title: '需求已发布', desc: '等待需求被承接，承接后将进行站内通知' },
        2: { title: '需求进行中', desc: '需求已被承接，请和承接人沟通后完成需求' },
        3: { title: '需求已完成', desc: '确认需求已完成，进行平台结算金额支付' },
        6: { title: '需求已结算', desc: '需求已成功结算，欢迎再次发布新需求' }
    }

    const accepterTexts: Record<number, { title: string; desc: string }> = {
        2: { title: '需求已承接', desc: '承接后，点击立即沟通/打电话可与需求方联系' },
        3: { title: '需求已完成', desc: '需求完成后，等待需求方进行金额结算' },
        6: { title: '需求已结算', desc: '金额已打款到您的账号，在我的账单可查看' }
    }

    if (role === 'owner') return ownerTexts[status] || { title: '', desc: '' }
    if (role === 'accepter') return accepterTexts[status] || { title: '', desc: '' }
    return { title: '', desc: '' }
})

const showPublisherCard = computed(() => {
    const role = userRole.value
    const status = demandStatus.value
    if (role === 'owner') return status === 1
    if (role === 'accepter') return [2, 3, 6].includes(status)
    return status === 1
})

const showAccepterCard = computed(() => {
    const role = userRole.value
    const status = demandStatus.value
    const hasAccepter = !!demandData.value?.accept_info
    if (role === 'owner') return [2, 3, 6].includes(status) && hasAccepter
    return false
})

const showPhotoSection = computed(() => {
    return demandData.value?.images?.length > 0
})

const hasBottomBar = computed(() => bottomButtons.value.length > 0)

// ============ 计算属性：底部按钮配置 ============
interface ButtonConfig {
    text: string
    action: string
    type: 'primary' | 'secondary' | 'disabled'
}

const bottomButtons = computed((): ButtonConfig[] => {
    const role = userRole.value
    const status = demandStatus.value

    if (status === 6) return []

    if (role === 'owner') {
        if (status === 1) {
            return [
                { text: '编辑需求', action: 'edit', type: 'secondary' },
                { text: '分享给邻居', action: 'share', type: 'primary' }
            ]
        }
        if (status === 2 || status === 3) {
            return [
                { text: '调整金额', action: 'adjust', type: 'secondary' },
                { text: status === 2 ? '确定完成' : '立即结算', action: status === 2 ? 'finish' : 'settle', type: 'primary' }
            ]
        }
    }

    if (role === 'accepter') {
        if (status === 1) {
            return [{ text: '承接需求', action: 'accept', type: 'primary' }]
        }
        if (status === 2) {
            return [
                { text: '取消承接', action: 'cancelAccept', type: 'secondary' },
                { text: '立即沟通', action: 'contact', type: 'primary' }
            ]
        }
        if (status === 3) {
            return [{ text: '等待结算', action: 'wait', type: 'disabled' }]
        }
    }

    if (role === 'stranger' && status === 1) {
        return [{ text: '承接需求', action: 'accept', type: 'primary' }]
    }

    return []
})

// ============ 计算属性：价格显示 ============
const priceDisplay = computed(() => {
    const data = demandData.value
    if (!data) return { value: '0', unit: '', extra: '' }

    const type = data.price_type
    if (type === 1) {
        return {
            value: String(data.hour_price || 0),
            unit: '/小时',
            extra: data.hours ? `× ${data.hours}小时` : ''
        }
    } else if (type === 3) {
        return {
            value: `${data.min_amount || 0}~${data.max_amount || 0}`,
            unit: '元',
            extra: ''
        }
    } else {
        return {
            value: String(data.amount || 0),
            unit: '/次',
            extra: ''
        }
    }
})

// ============ 计算属性：手机号显示 ============
const displayPhone = computed(() => {
    const phone = demandData.value?.contact_phone || ''
    if (userRole.value === 'stranger' && !demandData.value?.show_phone) {
        return hidePhoneNumber(phone)
    }
    return phone
})

// ============ 计算属性：完整地址 ============
const fullAddress = computed(() => {
    const data = demandData.value
    if (!data) return ''
    const parts = [data.community_name, data.address].filter(Boolean)
    return parts.join(' ')
})

// ============ 计算属性：确认弹窗内容 ============
const confirmModalTitle = computed(() => {
    const titles: Record<string, string> = {
        accept: '承接需求',
        cancelAccept: '取消承接',
        finish: '确认完成'
    }
    return titles[confirmActionType.value || ''] || '提示'
})

const confirmModalContent = computed(() => {
    const contents: Record<string, string> = {
        accept: '确定要承接这个需求吗？',
        cancelAccept: '确定要取消承接吗？',
        finish: '确定需求已完成吗？'
    }
    return contents[confirmActionType.value || ''] || ''
})

// ============ 生命周期 ============
// 微信小程序分享给好友
onShareAppMessage(() => {
    const data = demandData.value
    return {
        title: data?.title || '邻居需求',
        path: `/pages/demand/demand?id=${demandId.value}`,
        imageUrl: data?.images?.[0] || ''
    }
})

onLoad((options: any) => {
    if (options.id) {
        demandId.value = parseInt(options.id)
        loadDetail()
    } else {
        uni.$u.toast('参数错误')
    }
})

// 页面显示时刷新数据（从编辑页返回时触发）
onShow(() => {
    if (demandId.value) {
        loadDetail()
    }
})

// ============ 数据加载 ============
const loadDetail = async () => {
    loading.value = true
    try {
        demandData.value = await getDemandDetail(demandId.value)
    } catch (e) {
        console.error('加载详情失败', e)
        uni.$u.toast('需求不存在')
        setTimeout(() => {
            uni.navigateBack()
        }, 500)
    } finally {
        loading.value = false
    }
}

// ============ 工具函数 ============
const formatTime = (time: string | number, prefix?: string) => {
    if (!time) return ''
    let date: Date
    if (typeof time === 'string') {
        date = new Date(time.replace(/-/g, '/'))
    } else {
        date = new Date(time * 1000)
    }
    const y = date.getFullYear()
    const m = String(date.getMonth() + 1).padStart(2, '0')
    const d = String(date.getDate()).padStart(2, '0')
    const h = String(date.getHours()).padStart(2, '0')
    const min = String(date.getMinutes()).padStart(2, '0')
    const formatted = `${y}.${m}.${d} ${h}:${min}`
    return prefix ? `${prefix} ${formatted}` : formatted
}

const hidePhoneNumber = (phone: string) => {
    if (!phone) return ''
    return phone.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2')
}

const previewImage = (index: number) => {
    uni.previewImage({
        urls: demandData.value.images,
        current: index
    })
}

// ============ 按钮事件处理 ============
const handleButtonClick = (action: string) => {
    switch (action) {
        case 'edit':
            handleEdit()
            break
        case 'share':
            handleShare()
            break
        case 'adjust':
            handleAdjust()
            break
        case 'settle':
            handleSettle()
            break
        case 'contact':
            handleContact()
            break
        case 'wait':
            break
        case 'accept':
        case 'cancelAccept':
        case 'finish':
            confirmActionType.value = action as any
            showConfirmModal.value = true
            break
    }
}

const handleEdit = () => {
    uni.navigateTo({
        url: `/pages/publish-demand/publish-demand?id=${demandId.value}`
    })
}

const handleShare = () => {
    uni.$u.toast('功能开发中')
}

const handleAdjust = () => {
    adjustAmount.value = String(demandData.value?.amount || '')
    showAdjustPopup.value = true
}

const handleConfirmAdjust = async () => {
    const amount = parseFloat(adjustAmount.value)
    if (!amount || amount <= 0) {
        uni.$u.toast('请输入有效金额')
        return
    }
    if (submitting.value) return
    submitting.value = true
    try {
        await adjustDemandAmount(demandId.value, amount)
        uni.$u.toast('调整成功')
        showAdjustPopup.value = false
        loadDetail()
    } catch (e) {
        console.error('调整金额失败', e)
    } finally {
        submitting.value = false
    }
}

const handleSettle = async () => {
    if (submitting.value) return
    submitting.value = true
    try {
        await settleDemand(demandId.value)
        uni.$u.toast('结算成功')
        loadDetail()
    } catch (e) {
        console.error('结算失败', e)
    } finally {
        submitting.value = false
    }
}

const handleContact = async () => {
    // 确定对方用户ID
    const peerUserId = userRole.value === 'owner'
        ? demandData.value?.accept_info?.id
        : demandData.value?.user_info?.id

    if (!peerUserId) {
        uni.$u.toast('无法获取用户信息')
        return
    }

    uni.showLoading({ title: '加载中', mask: true })
    try {
        const res = await createChatSession(peerUserId)
        uni.hideLoading()
        if (res?.session_id) {
            const peerUser = {
                id: peerUserId,
                nickname: userRole.value === 'owner'
                    ? demandData.value?.accept_info?.nickname
                    : demandData.value?.user_info?.nickname,
                avatar: userRole.value === 'owner'
                    ? demandData.value?.accept_info?.avatar
                    : demandData.value?.user_info?.avatar
            }
            uni.navigateTo({
                url: `/pages/chat/chat?session_id=${res.session_id}&peer_user=${encodeURIComponent(JSON.stringify(peerUser))}`
            })
        }
    } catch (e) {
        uni.hideLoading()
        console.error('创建会话失败', e)
        uni.$u.toast('创建会话失败')
    }
}

const handleConfirmAction = async () => {
    if (!confirmActionType.value) return
    if (submitting.value) return
    submitting.value = true
    try {
        switch (confirmActionType.value) {
            case 'accept':
                await acceptDemand(demandId.value)
                uni.$u.toast('承接成功')
                break
            case 'cancelAccept':
                await cancelAcceptDemand(demandId.value)
                uni.$u.toast('已取消承接')
                break
            case 'finish':
                await finishDemand(demandId.value)
                uni.$u.toast('已确认完成')
                break
        }
        showConfirmModal.value = false
        loadDetail()
    } catch (e) {
        console.error('操作失败', e)
    } finally {
        submitting.value = false
    }
}
</script>

<style lang="scss" scoped>
.page {
    min-height: 100vh;
    background: #F3FAFA;
    padding-bottom: calc(145rpx + env(safe-area-inset-bottom));
}

// 加载状态
.loading-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding-top: 200rpx;
}

.loading-text {
    font-size: 28rpx;
    color: #999;
    margin-top: 20rpx;
}

// 流程说明卡片
.flow-card {
    background: #fff;
    border-radius: 24rpx;
    margin: 29rpx 29rpx 22rpx;
    padding: 22rpx 29rpx;
    display: flex;
    align-items: center;
}

.flow-icon {
    width: 50rpx;
    height: 52rpx;
    flex-shrink: 0;
    margin-right: 20rpx;
}

.flow-steps {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.flow-text {
    font-size: 26rpx;
    color: #222929;
}

.flow-arrow {
    font-size: 44rpx;
    color: #DADADA;
    margin: 0 12rpx;
}

// 状态卡片
.status-card {
    background: #fff;
    border-radius: 29rpx;
    margin: 29rpx;
    padding: 29rpx;
    display: flex;
    align-items: center;
}

.status-icon {
    width: 80rpx;
    height: 80rpx;
    background: #F3FAFA;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.icon-circle {
    width: 47rpx;
    height: 47rpx;
    border: 4rpx solid #222222;
    border-radius: 50%;
    position: relative;

    .check-mark {
        position: absolute;
        left: 14rpx;
        top: 7rpx;
        width: 14rpx;
        height: 24rpx;
        border-right: 4rpx solid #222222;
        border-bottom: 4rpx solid #222222;
        transform: rotate(45deg);
    }
}

.status-info {
    margin-left: 13rpx;
    flex: 1;
}

.status-title {
    font-size: 36rpx;
    font-weight: 500;
    color: #222222;
    display: block;
}

.status-desc {
    font-size: 25rpx;
    color: #666666;
    margin-top: 7rpx;
    display: block;
}

// 需求信息卡片
.demand-card {
    background: #fff;
    border-radius: 29rpx;
    margin: 0 29rpx 22rpx;
    padding: 29rpx;
}

.demand-title {
    font-size: 33rpx;
    font-weight: 500;
    color: #222222;
    display: block;
}

.demand-meta {
    display: flex;
    align-items: center;
    margin-top: 16rpx;
}

.type-tag {
    background: #F3FAFA;
    border-radius: 11rpx;
    padding: 7rpx 14rpx;
    margin-right: auto;
}

.type-text {
    font-size: 25rpx;
    font-weight: 500;
    color: #3A9B9C;
}

.price-symbol {
    font-size: 33rpx;
    font-weight: bold;
    color: #F04530;
}

.price-value {
    font-size: 44rpx;
    font-weight: bold;
    color: #F04530;
    margin-left: 4rpx;
}

.price-unit {
    font-size: 25rpx;
    font-weight: bold;
    color: #F04530;
    margin-left: 5rpx;
}

.price-hours {
    font-size: 25rpx;
    font-weight: bold;
    color: #F04530;
    margin-left: 10rpx;
}

// 通用区块样式
.content-card,
.publisher-card,
.receiver-card {
    background: #fff;
    border-radius: 29rpx;
    margin: 0 29rpx 22rpx;
    padding: 29rpx;
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 22rpx;
}

.dot-indicator {
    width: 15rpx;
    height: 15rpx;
    background: #00B6B4;
    border-radius: 50%;
    position: relative;

    &::after {
        content: '';
        position: absolute;
        width: 15rpx;
        height: 15rpx;
        background: rgba(125, 255, 254, 0.4);
        border-radius: 50%;
    }
}

.section-title {
    font-size: 29rpx;
    font-weight: 500;
    color: #222222;
    margin-left: 11rpx;
}

.detail-row {
    display: flex;
    margin-bottom: 22rpx;
}

.detail-label {
    font-size: 29rpx;
    color: #666666;
    width: 130rpx;
    flex-shrink: 0;
    white-space: nowrap;
    margin-right: 18rpx;
}

.detail-text {
    font-size: 29rpx;
    color: #222222;
    flex: 1;
    line-height: 40rpx;
}

// 照片区域
.photo-row {
    display: flex;
    align-items: flex-start;
}

.photo-list {
    flex: 1;
    white-space: nowrap;
    margin-top: 7rpx;
}

.photo-img {
    width: 156rpx;
    height: 116rpx;
    border-radius: 11rpx;
    margin-right: 14rpx;
    display: inline-block;
}

// 发布人/承接人信息
.publisher-info,
.receiver-info {
    background: #F3FAFA;
    border-radius: 15rpx;
    padding: 22rpx;
}

.publisher-row,
.receiver-row {
    display: flex;
    align-items: center;
}

.avatar {
    width: 62rpx;
    height: 62rpx;
    border-radius: 50%;
    flex-shrink: 0;
}

.publisher-text,
.receiver-text {
    margin-left: 14rpx;
    flex: 1;
}

.publisher-name,
.receiver-name {
    font-size: 29rpx;
    font-weight: 500;
    color: #333333;
    display: block;
}

.publisher-time,
.receiver-time {
    font-size: 25rpx;
    color: #999999;
    display: block;
    margin-top: 4rpx;
}

.contact-btn {
    background: #fff;
    border: 2rpx solid #00B6B4;
    border-radius: 60rpx;
    padding: 14rpx 22rpx;
}

.contact-text {
    font-size: 24rpx;
    font-weight: 500;
    color: #00B6B4;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-top: 22rpx;
}

.info-label {
    font-size: 29rpx;
    color: #616B6B;
}

.info-value {
    font-size: 29rpx;
    color: #222222;
}

// 底部占位
.bottom-placeholder {
    height: 145rpx;
}

// 底部操作栏
.bottom-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    padding: 22rpx 36rpx calc(22rpx + env(safe-area-inset-bottom));
    display: flex;
    gap: 22rpx;
}

// 左边按钮 - 次要操作
.secondary-btn {
    width: 236rpx;
    height: 98rpx;
    background: #F6F6F6;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.secondary-text {
    font-size: 29rpx;
    color: #999999;
}

// 右边按钮 - 主要操作
.primary-btn {
    flex: 1;
    height: 98rpx;
    background: #00B6B4;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.primary-text {
    font-size: 33rpx;
    font-weight: 500;
    color: #fff;
}

// 分享按钮 - 移除 button 默认样式
.share-btn {
    padding: 0;
    margin: 0;
    border: none;
    background: #00B6B4;
    line-height: normal;

    &::after {
        border: none;
    }
}

.disabled-btn {
    flex: 1;
    height: 98rpx;
    background: #DADADA;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.disabled-text {
    font-size: 33rpx;
    font-weight: 500;
    color: #999999;
}

// 调整金额弹窗
.adjust-popup {
    padding: 0 36rpx calc(36rpx + env(safe-area-inset-bottom));
}

.popup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 51rpx 0 36rpx;
}

.popup-title {
    font-size: 40rpx;
    font-weight: 500;
    color: #222929;
}

.close-btn {
    width: 44rpx;
    height: 44rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-icon {
    font-size: 44rpx;
    color: #9CA6A6;
    line-height: 1;
}

.popup-content {
    padding-bottom: 36rpx;
}

.popup-label {
    font-size: 29rpx;
    color: #616B6B;
    display: block;
    margin-bottom: 14rpx;
}

.current-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 36rpx;
}

.current-amount {
    font-size: 36rpx;
    font-weight: bold;
    color: #222929;
}

.amount-input-row {
    display: flex;
    align-items: center;
    background: #F6F6F6;
    border-radius: 15rpx;
    padding: 22rpx 29rpx;
}

.amount-symbol {
    font-size: 40rpx;
    font-weight: bold;
    color: #00A2A0;
    margin-right: 14rpx;
}

.amount-input {
    flex: 1;
    font-size: 40rpx;
    font-weight: bold;
    color: #00A2A0;
}

.popup-buttons {
    display: flex;
    gap: 40rpx;
}

.btn-cancel {
    width: 236rpx;
    height: 98rpx;
    background: #F6F6F6;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-cancel-text {
    font-size: 29rpx;
    color: #9CA6A6;
}

.btn-confirm {
    flex: 1;
    height: 98rpx;
    background: #00B6B4;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-confirm-text {
    font-size: 29rpx;
    font-weight: bold;
    color: #fff;
}
</style>
