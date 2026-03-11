<template>
    <view class="page">
        <!-- 顶部提示条 -->
        <view class="tip-bar" v-if="!isVerified">
            <text class="tip-text">您还未进行业主验证，业主与住户才能发布！</text>
            <view class="cert-btn" @click="handleGoVerify">
                <text class="cert-btn-text">去认证</text>
            </view>
        </view>

        <!-- 表单区域 -->
        <scroll-view scroll-y class="form-area">
            <!-- 需求标题 -->
            <view class="form-item">
                <text class="form-label">需求标题</text>
                <view class="title-input-row">
                    <input
                        class="title-input"
                        v-model="formData.title"
                        placeholder="简单描述 例:找初三英语家教2小时"
                        placeholder-style="color: #DADADA; font-size: 33rpx;"
                        maxlength="30"
                        @input="handleTitleInput"
                    />
                    <text class="char-count">{{ formData.title.length }}/30</text>
                </view>
            </view>

            <!-- 需求类型 -->
            <view class="form-item">
                <view class="type-header">
                    <text class="form-label">需求类型</text>
                </view>
                <view class="type-grid">
                    <view
                        v-for="item in categoryList"
                        :key="item.id"
                        class="type-item"
                        :class="{ selected: formData.category_id === item.id }"
                        @click="handleSelectCategory(item.id)"
                    >
                        <text class="type-text" :class="{ selected: formData.category_id === item.id }">{{ item.name }}</text>
                    </view>
                </view>
            </view>

            <!-- 需求金额 -->
            <view class="form-item" @click="handlePriceClick">
                <text class="form-label">需求金额</text>
                <view class="price-row">
                    <template v-if="priceType === 'range'">
                        <text class="price-symbol">¥</text>
                        <text class="price-value">{{ formData.min_amount.toFixed(2) }}</text>
                        <text class="price-range-text">至</text>
                        <text class="price-symbol">¥</text>
                        <text class="price-value">{{ formData.max_amount.toFixed(2) }}</text>
                    </template>
                    <template v-else>
                        <text class="price-symbol">¥</text>
                        <text class="price-value">{{ displayPrice.toFixed(2) }}</text>
                        <text class="price-unit">/{{ priceType === 'hour' ? '小时' : '次' }}</text>
                    </template>
                    <view class="price-arrow"></view>
                </view>
            </view>

            <!-- 需求描述 -->
            <view class="form-item">
                <text class="form-label">需求描述</text>
                <textarea
                    class="desc-textarea"
                    v-model="formData.description"
                    placeholder="描述需要帮忙的事请。例如丢垃圾、代买物品、取快递、代关门窗、宠物养护/上门喂养等。描述越清晰越好～"
                    placeholder-style="color: #9CA6A6; font-size: 27rpx;"
                    :maxlength="500"
                />
            </view>

            <!-- 上传照片 -->
            <view class="form-item">
                <view class="upload-header">
                    <text class="form-label">上传照片</text>
                    <text class="upload-hint">例:电器维修部位</text>
                </view>
                <view class="upload-list">
                    <view
                        v-for="(img, index) in formData.images"
                        :key="index"
                        class="upload-item"
                    >
                        <image class="upload-img" :src="img" mode="aspectFill" />
                        <view class="upload-delete" @click="handleDeleteImage(index)">
                            <text class="delete-icon">×</text>
                        </view>
                    </view>
                    <view
                        v-if="formData.images.length < 9"
                        class="upload-box"
                        @click="handleUpload"
                    >
                        <view class="upload-icon">
                            <view class="upload-icon-add"></view>
                            <view class="upload-icon-circle"></view>
                        </view>
                        <text class="upload-text">立即上传</text>
                    </view>
                </view>
            </view>

            <!-- 是否紧急发布 -->
            <view class="switch-item" @click="handleToggleUrgent">
                <view class="switch-info">
                    <text class="switch-label">是否紧急发布</text>
                    <text class="switch-hint">说明：紧急需求将收取3%的服务费，完成后收取。</text>
                </view>
                <view class="switch-off" :class="{ 'switch-on': formData.is_urgent === 1 }">
                    <view class="switch-off-inner" :class="{ 'switch-on-inner': formData.is_urgent === 1 }"></view>
                </view>
            </view>

            <!-- 是否提供真实手机号 -->
            <view class="switch-item" @click="handleToggleShowPhone">
                <text class="switch-label">是否提供真实手机号</text>
                <view class="switch-off" :class="{ 'switch-on': formData.show_phone === 1 }">
                    <view class="switch-off-inner" :class="{ 'switch-on-inner': formData.show_phone === 1 }"></view>
                </view>
            </view>
        </scroll-view>

        <!-- 底部区域 -->
        <view class="bottom-area">
            <view class="agreement-row" @click="handleToggleAgreement">
                <view class="agreement-checkbox" :class="{ checked: agreedProtocol }">
                    <text v-if="agreedProtocol" class="check-icon">✓</text>
                </view>
                <text class="agreement-text">
                    已阅读并同意<text class="agreement-link" @click.stop="handleViewProtocol('publish')">《平台需求发布规则》</text>，<text class="agreement-link" @click.stop="handleViewProtocol('accept')">《需求承接协议》</text>
                </text>
            </view>
            <view class="btn-row">
                <view class="delete-btn" v-if="isEdit" @click="handleDelete">
                    <text class="delete-btn-text">删除</text>
                </view>
                <view class="publish-btn" :class="{ disabled: submitting }" @click="handlePublish">
                    <text class="publish-btn-text">{{ submitting ? '发布中...' : '发布需求' }}</text>
                </view>
            </view>
        </view>

        <!-- 金额弹窗 -->
        <price-popup
            v-model:show="showPricePopup"
            v-model="displayPrice"
            v-model:price-type="priceType"
            v-model:min-price="formData.min_amount"
            v-model:max-price="formData.max_amount"
            v-model:hours="formData.hours"
            @confirm="handlePriceConfirm"
        />
    </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useUserStore } from '@/stores/user'
import { getDemandCategoryLists, publishDemand, editDemand } from '@/api/demand'
import { getUserVerifyDetail } from '@/api/userVerify'
import { getUserAddress } from '@/api/community'
import { uploadImage } from '@/api/app'
import PricePopup from '@/components/price-popup/price-popup.vue'

const userStore = useUserStore()

// 是否编辑模式
const isEdit = ref(false)
const editId = ref(0)

// 是否已认证
const isVerified = ref(false)

// 分类列表
const categoryList = ref<any[]>([])

// 协议勾选
const agreedProtocol = ref(false)

// 提交中状态
const submitting = ref(false)

// 表单数据
const formData = ref({
    category_id: 0,
    title: '',
    description: '',
    images: [] as string[],
    price_type: 1,  // 1=按小时，2=按次，3=按范围
    hours: 0,
    hour_price: 0,
    amount: 0,
    min_amount: 0,
    max_amount: 0,
    community_id: 0,
    address: '',
    contact_name: '',
    contact_phone: '',
    show_phone: 0,
    is_urgent: 0
})

// 金额弹窗
const showPricePopup = ref(false)
const priceType = ref<'hour' | 'times' | 'range'>('hour')
const displayPrice = ref(80)

// 获取分类列表
const fetchCategories = async () => {
    try {
        const data = await getDemandCategoryLists()
        categoryList.value = data || []
    } catch (e) {
        console.error('获取分类失败', e)
    }
}

// 检查认证状态
const checkVerifyStatus = async () => {
    try {
        const data = await getUserVerifyDetail()
        isVerified.value = data && data.status === 1
    } catch (e) {
        isVerified.value = false
    }
}

// 获取用户地址
const fetchUserAddress = async () => {
    try {
        const data = await getUserAddress()
        if (data && data.community_id) {
            formData.value.community_id = data.community_id
            const addressParts = []
            if (data.building) addressParts.push(data.building)
            if (data.room) addressParts.push(data.room)
            formData.value.address = addressParts.join(' ')

            // 填充联系人和电话
            if (userStore.userInfo) {
                formData.value.contact_name = userStore.userInfo.nickname || ''
                formData.value.contact_phone = userStore.userInfo.mobile || ''
            }
        }
    } catch (e) {
        console.error('获取地址失败', e)
    }
}

// 标题输入
const handleTitleInput = (e: any) => {
    formData.value.title = e.detail.value
}

// 选择分类
const handleSelectCategory = (id: number) => {
    formData.value.category_id = id
}

// 点击金额区域
const handlePriceClick = () => {
    showPricePopup.value = true
}

// 金额确认
const handlePriceConfirm = () => {
    // priceType 映射：'hour' → 1, 'times' → 2, 'range' → 3
    if (priceType.value === 'hour') {
        formData.value.price_type = 1
        formData.value.hour_price = displayPrice.value
        // hours 已经通过 v-model:hours 双向绑定更新
    } else if (priceType.value === 'times') {
        formData.value.price_type = 2
        formData.value.amount = displayPrice.value
    } else {
        formData.value.price_type = 3
        // min_amount 和 max_amount 已经通过 v-model 双向绑定更新
    }
}

// 图片上传
const handleUpload = () => {
    const remain = 9 - formData.value.images.length
    if (remain <= 0) {
        uni.$u.toast('最多上传9张图片')
        return
    }

    uni.chooseImage({
        count: remain,
        sizeType: ['compressed'],
        sourceType: ['album', 'camera'],
        success: async (res) => {
            uni.showLoading({ title: '上传中...' })
            try {
                for (const path of res.tempFilePaths) {
                    const result = await uploadImage(path, userStore.token || undefined)
                    if (result && result.uri) {
                        formData.value.images.push(result.uri)
                    }
                }
            } catch (e) {
                uni.$u.toast('上传失败')
            } finally {
                uni.hideLoading()
            }
        }
    })
}

// 删除图片
const handleDeleteImage = (index: number) => {
    formData.value.images.splice(index, 1)
}

// 切换紧急发布
const handleToggleUrgent = () => {
    formData.value.is_urgent = formData.value.is_urgent === 1 ? 0 : 1
}

// 切换显示手机号
const handleToggleShowPhone = () => {
    formData.value.show_phone = formData.value.show_phone === 1 ? 0 : 1
}

// 切换协议勾选
const handleToggleAgreement = () => {
    agreedProtocol.value = !agreedProtocol.value
}

// 去认证
const handleGoVerify = () => {
    uni.navigateTo({
        url: '/packages/pages/owner-verify/owner-verify'
    })
}

// 查看协议
const handleViewProtocol = (type: string) => {
    // TODO: 跳转协议页面
    console.log('查看协议', type)
}

// 表单验证
const validateForm = (): boolean => {
    if (!formData.value.title.trim()) {
        uni.$u.toast('请输入需求标题')
        return false
    }
    if (!formData.value.category_id) {
        uni.$u.toast('请选择需求类型')
        return false
    }
    if (!formData.value.description.trim()) {
        uni.$u.toast('请输入需求描述')
        return false
    }

    // 验证金额
    if (formData.value.price_type === 1) {
        if (!formData.value.hour_price || formData.value.hour_price <= 0) {
            uni.$u.toast('请设置金额')
            return false
        }
        if (!formData.value.hours || formData.value.hours <= 0) {
            uni.$u.toast('请设置小时数')
            return false
        }
    } else if (formData.value.price_type === 2) {
        if (!formData.value.amount || formData.value.amount <= 0) {
            uni.$u.toast('请设置金额')
            return false
        }
    } else if (formData.value.price_type === 3) {
        if (!formData.value.min_amount || !formData.value.max_amount) {
            uni.$u.toast('请设置金额范围')
            return false
        }
        if (formData.value.min_amount > formData.value.max_amount) {
            uni.$u.toast('最小金额不能大于最大金额')
            return false
        }
    }

    if (!formData.value.community_id) {
        uni.$u.toast('请先设置小区地址')
        return false
    }
    if (!formData.value.address.trim()) {
        uni.$u.toast('请输入详细地址')
        return false
    }
    if (!formData.value.contact_name.trim()) {
        uni.$u.toast('请输入联系人')
        return false
    }
    if (!formData.value.contact_phone.trim()) {
        uni.$u.toast('请输入联系电话')
        return false
    }
    if (!/^1[3-9]\d{9}$/.test(formData.value.contact_phone)) {
        uni.$u.toast('请输入正确的手机号')
        return false
    }
    if (!agreedProtocol.value) {
        uni.$u.toast('请阅读并同意协议')
        return false
    }
    return true
}

// 提交发布
const handlePublish = async () => {
    if (submitting.value) return
    if (!validateForm()) return

    submitting.value = true
    try {
        const submitData = {
            category_id: formData.value.category_id,
            title: formData.value.title,
            description: formData.value.description,
            images: formData.value.images,
            price_type: formData.value.price_type,
            hours: formData.value.hours || undefined,
            hour_price: formData.value.hour_price || undefined,
            amount: formData.value.amount || undefined,
            min_amount: formData.value.min_amount || undefined,
            max_amount: formData.value.max_amount || undefined,
            community_id: formData.value.community_id,
            address: formData.value.address,
            contact_name: formData.value.contact_name,
            contact_phone: formData.value.contact_phone,
            show_phone: formData.value.show_phone,
            is_urgent: formData.value.is_urgent
        }

        let result
        if (isEdit.value && editId.value) {
            result = await editDemand({ ...submitData, id: editId.value })
        } else {
            result = await publishDemand(submitData)
        }

        // 跳转到成功页面，传递需求ID和编号
        uni.redirectTo({
            url: `/pages/publish-success/publish-success?id=${result.id}&demand_no=${result.demand_no}`
        })
    } catch (e) {
        console.error('发布失败', e)
    } finally {
        submitting.value = false
    }
}

// 删除（编辑模式下）
const handleDelete = () => {
    uni.showModal({
        title: '提示',
        content: '确定要删除该需求吗？',
        success: (res) => {
            if (res.confirm) {
                // TODO: 调用删除接口
                uni.navigateBack()
            }
        }
    })
}

onMounted(() => {
    fetchCategories()
    checkVerifyStatus()
    fetchUserAddress()
})
</script>

<style lang="scss" scoped>
.page {
    min-height: 100vh;
    background: #fff;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
}

// 顶部提示条
.tip-bar {
    background: #F3FAFA;
    padding: 22rpx 36rpx;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-sizing: border-box;
    width: 100%;

    .tip-text {
        font-size: 25rpx;
        color: #222929;
    }

    .cert-btn {
        background: #00B6B4;
        border-radius: 60rpx;
        padding: 12rpx 30rpx;

        .cert-btn-text {
            font-size: 24rpx;
            font-weight: 500;
            color: #fff;
        }
    }
}

// 表单区域
.form-area {
    flex: 1;
    width: 100%;
    padding: 0 36rpx;
    box-sizing: border-box;
}

.form-item {
    padding: 38rpx 0;
    border-bottom: 2rpx solid #F5F5F5;
}

.form-label {
    font-size: 29rpx;
    font-weight: 500;
    color: #616B6B;
    display: block;
}

// 需求标题
.title-input-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 29rpx;
}

.title-input {
    flex: 1;
    font-size: 33rpx;
    color: #222929;
    box-sizing: border-box;
}

.char-count {
    font-size: 22rpx;
    color: #DADADA;
}

// 需求类型
.type-header {
    display: flex;
    align-items: center;
    margin-bottom: 29rpx;

    .form-label {
        margin-bottom: 0;
    }
}

.type-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 18rpx;
}

.type-item {
    padding: 16rpx 22rpx;
    border: 2rpx solid #00B6B4;
    border-radius: 11rpx;
    background: #fff;

    .type-text {
        font-size: 29rpx;
        color: #00B6B4;
    }

    &.selected {
        background: #00B6B4;

        .type-text {
            color: #fff;
        }
    }
}

// 需求金额
.price-row {
    display: flex;
    align-items: center;
    margin-top: 25rpx;

    .price-symbol {
        font-size: 33rpx;
        font-weight: bold;
        color: #00A2A0;
    }

    .price-value {
        font-size: 44rpx;
        font-weight: bold;
        color: #00A2A0;
        margin-left: 13rpx;
    }

    .price-unit {
        font-size: 25rpx;
        font-weight: bold;
        color: #00A2A0;
        margin-left: 5rpx;
    }

    .price-range-text {
        font-size: 36rpx;
        font-weight: 500;
        color: #222929;
        margin: 0 18rpx;
    }

    .price-arrow {
        width: 9rpx;
        height: 9rpx;
        border-right: 3rpx solid #DADADA;
        border-bottom: 3rpx solid #DADADA;
        transform: rotate(45deg);
        margin-left: auto;
    }
}

// 需求描述
.desc-textarea {
    width: 100%;
    height: 160rpx;
    font-size: 27rpx;
    color: #222929;
    line-height: 44rpx;
    margin-top: 29rpx;
    box-sizing: border-box;
}

// 上传照片
.upload-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 29rpx;

    .form-label {
        margin-bottom: 0;
    }

    .upload-hint {
        font-size: 27rpx;
        color: #9CA6A6;
    }
}

.upload-list {
    display: flex;
    flex-wrap: wrap;
    gap: 18rpx;
}

.upload-item {
    width: 145rpx;
    height: 145rpx;
    position: relative;

    .upload-img {
        width: 100%;
        height: 100%;
        border-radius: 15rpx;
    }

    .upload-delete {
        position: absolute;
        top: -10rpx;
        right: -10rpx;
        width: 36rpx;
        height: 36rpx;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;

        .delete-icon {
            color: #fff;
            font-size: 24rpx;
            line-height: 1;
        }
    }
}

.upload-box {
    width: 145rpx;
    height: 145rpx;
    background: #F3FAFA;
    border-radius: 15rpx;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.upload-icon {
    position: relative;
    width: 49rpx;
    height: 40rpx;

    .upload-icon-add {
        width: 100%;
        height: 5rpx;
        background: #00B6B4;
        border-radius: 3rpx;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }

    .upload-icon-circle {
        width: 18rpx;
        height: 18rpx;
        border: 5rpx solid #00B6B4;
        border-radius: 50%;
        position: absolute;
        bottom: 0;
        right: 50%;
        transform: translateX(50%);
    }
}

.upload-text {
    font-size: 25rpx;
    font-weight: 500;
    color: #616B6B;
    margin-top: 11rpx;
}

// 开关选项
.switch-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 34rpx 0;
    border-bottom: 2rpx solid #F5F5F5;
}

.switch-info {
    display: flex;
    flex-direction: column;
}

.switch-label {
    font-size: 29rpx;
    font-weight: 500;
    color: #616B6B;
}

.switch-hint {
    font-size: 24rpx;
    color: #9CA6A6;
    margin-top: 2rpx;
}

.switch-off {
    width: 102rpx;
    height: 55rpx;
    background: #F6F6F6;
    border-radius: 40rpx;
    display: flex;
    align-items: center;
    padding: 4rpx;

    &.switch-on {
        background: #00B6B4;
        justify-content: flex-end;
    }

    .switch-off-inner {
        width: 47rpx;
        height: 47rpx;
        background: #fff;
        border-radius: 50%;
        transition: all 0.2s;

        &.switch-on-inner {
            // 样式通过父级控制
        }
    }
}

// 底部区域
.bottom-area {
    background: #F3FAFA;
    padding: 15rpx 36rpx calc(15rpx + env(safe-area-inset-bottom));
    box-sizing: border-box;
    width: 100%;
}

.agreement-row {
    display: flex;
    align-items: flex-start;
    padding: 15rpx 0;

    .agreement-checkbox {
        width: 29rpx;
        height: 29rpx;
        border: 3rpx solid #00B6B4;
        border-radius: 50%;
        margin-right: 18rpx;
        flex-shrink: 0;
        margin-top: 4rpx;
        display: flex;
        align-items: center;
        justify-content: center;

        &.checked {
            background: #00B6B4;
        }

        .check-icon {
            color: #fff;
            font-size: 20rpx;
            line-height: 1;
        }
    }

    .agreement-text {
        font-size: 25rpx;
        color: #9CA6A6;
        line-height: 44rpx;
    }

    .agreement-link {
        color: #00A2A0;
    }
}

.btn-row {
    display: flex;
    align-items: center;
    margin-top: 22rpx;
    gap: 22rpx;
}

.delete-btn {
    width: 236rpx;
    height: 98rpx;
    background: #F6F6F6;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;

    .delete-btn-text {
        font-size: 29rpx;
        color: #9CA6A6;
    }
}

.publish-btn {
    flex: 1;
    height: 98rpx;
    background: #00B6B4;
    border-radius: 58rpx;
    display: flex;
    align-items: center;
    justify-content: center;

    &.disabled {
        opacity: 0.6;
    }

    .publish-btn-text {
        font-size: 29rpx;
        font-weight: 500;
        color: #fff;
    }
}
</style>
