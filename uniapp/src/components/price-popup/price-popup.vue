<template>
    <u-popup v-model="showPopup" mode="bottom" border-radius="29" :mask-close-able="false">
        <view class="popup-content">
            <!-- 标题栏 -->
            <view class="popup-header">
                <text class="popup-title">填写需求金额</text>
                <view class="close-btn" v-if="currentType === 'range'" @click="handleClose">
                    <text class="close-icon">×</text>
                </view>
            </view>

            <!-- 收费类型选项卡 -->
            <view class="type-tabs">
                <view
                    class="type-tab"
                    :class="{ active: currentType === 'hour' }"
                    @click="handleTypeChange('hour')"
                >
                    <text class="tab-text">按小时收费</text>
                </view>
                <view
                    class="type-tab"
                    :class="{ active: currentType === 'times' }"
                    @click="handleTypeChange('times')"
                >
                    <text class="tab-text">按次数收费</text>
                </view>
                <view
                    class="type-tab"
                    :class="{ active: currentType === 'range' }"
                    @click="handleTypeChange('range')"
                >
                    <text class="tab-text">按范围填写</text>
                </view>
            </view>

            <!-- 金额输入区域 -->
            <view class="price-input-area">
                <!-- 按小时 -->
                <view class="price-hour" v-if="currentType === 'hour'">
                    <view class="hour-row">
                        <text class="price-symbol">¥</text>
                        <input
                            class="price-input"
                            type="digit"
                            v-model="priceValue"
                            placeholder="0.00"
                            placeholder-style="color: #DADADA;"
                        />
                        <text class="price-unit">/小时</text>
                    </view>
                    <view class="hour-row">
                        <text class="hour-label">总时长</text>
                        <input
                            class="hour-input"
                            type="digit"
                            v-model="hoursValue"
                            placeholder="0"
                            placeholder-style="color: #DADADA;"
                        />
                        <text class="hour-unit">小时</text>
                    </view>
                </view>
                <!-- 按次数 -->
                <view class="price-single" v-else-if="currentType === 'times'">
                    <text class="price-symbol">¥</text>
                    <input
                        class="price-input"
                        type="digit"
                        v-model="priceValue"
                        placeholder="0.00"
                        placeholder-style="color: #DADADA;"
                    />
                    <text class="price-unit">/次</text>
                </view>
                <!-- 按范围 -->
                <view class="price-range" v-else>
                    <text class="price-symbol">¥</text>
                    <input
                        class="price-input"
                        type="digit"
                        v-model="minPriceValue"
                        placeholder="0.00"
                        placeholder-style="color: #DADADA;"
                    />
                    <text class="range-text">至</text>
                    <text class="price-symbol">¥</text>
                    <input
                        class="price-input"
                        type="digit"
                        v-model="maxPriceValue"
                        placeholder="0.00"
                        placeholder-style="color: #DADADA;"
                    />
                </view>
            </view>

            <!-- 说明文字 -->
            <view class="price-tips">
                <text class="tips-text">说明：平台将按照市场价填写默认的收费金额，用户可作为参考，也可自行填写任务金额。</text>
                <text class="tips-text">家教：80元/小时</text>
                <text class="tips-text">陪诊：280元/半天（包含来回接送、停车费等）</text>
                <text class="tips-text">卫生清洁：35元/小时</text>
                <text class="tips-text">接送孩子（5公里内）：30元/次</text>
                <text class="tips-text"></text>
                <text class="tips-text">顺风车：0.8元/公里（高速费另算）</text>
            </view>

            <!-- 底部按钮 -->
            <view class="popup-buttons">
                <view class="btn-cancel" @click="handleCancel">
                    <text class="btn-cancel-text">取消</text>
                </view>
                <view class="btn-confirm" @click="handleConfirm">
                    <text class="btn-confirm-text">确定</text>
                </view>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'

interface Props {
    show: boolean
    modelValue?: number
    priceType?: 'hour' | 'times' | 'range'
    minPrice?: number
    maxPrice?: number
    hours?: number
}

const props = withDefaults(defineProps<Props>(), {
    show: false,
    modelValue: 0,
    priceType: 'hour',
    minPrice: 0,
    maxPrice: 0,
    hours: 1
})

const emit = defineEmits<{
    'update:show': [value: boolean]
    'update:modelValue': [value: number]
    'update:priceType': [value: string]
    'update:minPrice': [value: number]
    'update:maxPrice': [value: number]
    'update:hours': [value: number]
    'confirm': []
    'cancel': []
}>()

// 双向绑定 show
const showPopup = computed({
    get() {
        return props.show
    },
    set(val) {
        emit('update:show', val)
    }
})

// 本地数据
const priceValue = ref('80.00')
const minPriceValue = ref('150.00')
const maxPriceValue = ref('210.00')
const hoursValue = ref('1')
const currentType = ref<'hour' | 'times' | 'range'>(props.priceType)

// 监听 props 变化
watch(() => props.priceType, (val) => {
    currentType.value = val
})

watch(() => props.modelValue, (val) => {
    if (val) {
        priceValue.value = val.toFixed(2)
    }
})

watch(() => props.minPrice, (val) => {
    if (val) {
        minPriceValue.value = val.toFixed(2)
    }
})

watch(() => props.maxPrice, (val) => {
    if (val) {
        maxPriceValue.value = val.toFixed(2)
    }
})

watch(() => props.hours, (val) => {
    if (val) {
        hoursValue.value = val.toString()
    }
})

// 切换收费类型
const handleTypeChange = (type: 'hour' | 'times' | 'range') => {
    currentType.value = type
    emit('update:priceType', type)
}

// 关闭弹窗
const handleClose = () => {
    showPopup.value = false
}

// 取消
const handleCancel = () => {
    emit('cancel')
    showPopup.value = false
}

// 确定
const handleConfirm = () => {
    if (currentType.value === 'range') {
        emit('update:minPrice', parseFloat(minPriceValue.value) || 0)
        emit('update:maxPrice', parseFloat(maxPriceValue.value) || 0)
    } else if (currentType.value === 'hour') {
        emit('update:modelValue', parseFloat(priceValue.value) || 0)
        emit('update:hours', parseFloat(hoursValue.value) || 1)
    } else {
        emit('update:modelValue', parseFloat(priceValue.value) || 0)
    }
    emit('confirm')
    showPopup.value = false
}
</script>

<style lang="scss" scoped>
.popup-content {
    padding: 0 36rpx;
    padding-bottom: calc(36rpx + env(safe-area-inset-bottom));
}

// 标题栏
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

// 收费类型选项卡
.type-tabs {
    display: flex;
    gap: 18rpx;
}

.type-tab {
    flex: 1;
    height: 76rpx;
    background: #F3FAFA;
    border-radius: 15rpx;
    display: flex;
    align-items: center;
    justify-content: center;

    &.active {
        background: #00B6B4;

        .tab-text {
            color: #fff;
        }
    }
}

.tab-text {
    font-size: 29rpx;
    font-weight: 500;
    color: #222929;
}

// 金额输入区域
.price-input-area {
    margin-top: 36rpx;
}

.price-hour {
    display: flex;
    flex-direction: column;
    gap: 25rpx;
}

.hour-row {
    display: flex;
    align-items: center;
}

.hour-label {
    font-size: 29rpx;
    color: #222929;
    width: 100rpx;
}

.hour-input {
    flex: 1;
    font-size: 40rpx;
    font-weight: bold;
    color: #00A2A0;
    background: transparent;
    border: none;
    padding: 0 10rpx;
    text-align: center;
}

.hour-unit {
    font-size: 29rpx;
    color: #222929;
}

.price-single {
    display: flex;
    align-items: center;
}

.price-range {
    display: flex;
    align-items: center;
}

.price-symbol {
    font-size: 33rpx;
    font-weight: bold;
    color: #00A2A0;
}

.price-input {
    width: 180rpx;
    font-size: 55rpx;
    font-weight: bold;
    color: #00A2A0;
    background: transparent;
    border: none;
    padding: 0 10rpx;
}

.price-unit {
    font-size: 29rpx;
    font-weight: bold;
    color: #00A2A0;
    margin-left: auto;
}

.range-text {
    font-size: 36rpx;
    font-weight: 500;
    color: #222929;
    margin: 0 36rpx;
}

// 说明文字
.price-tips {
    margin-top: 51rpx;
}

.tips-text {
    font-size: 29rpx;
    color: #9CA6A6;
    line-height: 44rpx;
    display: block;
}

// 底部按钮
.popup-buttons {
    display: flex;
    gap: 40rpx;
    margin-top: 36rpx;
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