<template>
    <view class="page">
        <!-- 顶部提示条 -->
        <view class="tip-bar">
            <text class="tip-text">您还未进行业主验证，业主与住户才能发布！</text>
            <view class="cert-btn">
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
                        placeholder="简单描述 例:找初三英语家教2小时"
                        placeholder-style="color: #DADADA; font-size: 33rpx;"
                        maxlength="30"
                    />
                    <text class="char-count">0/30</text>
                </view>
            </view>

            <!-- 需求类型 -->
            <view class="form-item">
                <view class="type-header">
                    <text class="form-label">需求类型</text>
                    <text class="type-hint">类型说明</text>
                    <view class="type-checkbox"></view>
                </view>
                <view class="type-grid">
                    <view class="type-item">
                        <text class="type-text">轻需求</text>
                    </view>
                    <view class="type-item">
                        <text class="type-text">接送需求</text>
                    </view>
                    <view class="type-item">
                        <text class="type-text">维修需求</text>
                    </view>
                    <view class="type-item selected">
                        <text class="type-text selected">陪伴需求</text>
                    </view>
                    <view class="type-item">
                        <text class="type-text">物品交易</text>
                    </view>
                    <view class="type-item">
                        <text class="type-text">家政服务</text>
                    </view>
                    <view class="type-item">
                        <text class="type-text">其他需求</text>
                    </view>
                </view>
            </view>

            <!-- 需求金额 -->
            <view class="form-item" @click="handlePriceClick">
                <text class="form-label">需求金额</text>
                <view class="price-row">
                    <template v-if="priceType === 'range'">
                        <text class="price-symbol">¥</text>
                        <text class="price-value">{{ minPrice.toFixed(2) }}</text>
                        <text class="price-range-text">至</text>
                        <text class="price-symbol">¥</text>
                        <text class="price-value">{{ maxPrice.toFixed(2) }}</text>
                    </template>
                    <template v-else>
                        <text class="price-symbol">¥</text>
                        <text class="price-value">{{ price.toFixed(2) }}</text>
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
                    placeholder="描述需要帮忙的事请。例如丢垃圾、代买物品、取快递、代关门窗、宠物养护/上门喂养等。描述越清晰越好～"
                    placeholder-style="color: #9CA6A6; font-size: 27rpx;"
                    :maxlength="200"
                />
            </view>

            <!-- 上传照片 -->
            <view class="form-item">
                <view class="upload-header">
                    <text class="form-label">上传照片</text>
                    <text class="upload-hint">例:电器维修部位</text>
                </view>
                <view class="upload-box">
                    <view class="upload-icon">
                        <view class="upload-icon-add"></view>
                        <view class="upload-icon-circle"></view>
                    </view>
                    <text class="upload-text">立即上传</text>
                </view>
            </view>

            <!-- 是否紧急发布 -->
            <view class="switch-item">
                <view class="switch-info">
                    <text class="switch-label">是否紧急发布</text>
                    <text class="switch-hint">说明：紧急需求将收取3%的服务费，完成后收取。</text>
                </view>
                <view class="switch-off">
                    <view class="switch-off-inner"></view>
                </view>
            </view>

            <!-- 是否提供真实手机号 -->
            <view class="switch-item">
                <text class="switch-label">是否提供真实手机号</text>
                <view class="switch-on">
                    <view class="switch-on-inner"></view>
                </view>
            </view>
        </scroll-view>

        <!-- 底部区域 -->
        <view class="bottom-area">
            <view class="agreement-row">
                <view class="agreement-checkbox"></view>
                <text class="agreement-text">
                    已阅读并同意<text class="agreement-link">《平台需求发布规则》</text>，<text class="agreement-link">《需求承接协议》</text>
                </text>
            </view>
            <view class="btn-row">
                <view class="delete-btn">
                    <text class="delete-btn-text">删除</text>
                </view>
                <view class="publish-btn" @click="handlePublish">
                    <text class="publish-btn-text">发布需求</text>
                </view>
            </view>
        </view>

        <!-- 金额弹窗 -->
        <price-popup
            v-model:show="showPricePopup"
            v-model="price"
            v-model:price-type="priceType"
            v-model:min-price="minPrice"
            v-model:max-price="maxPrice"
            @confirm="handlePriceConfirm"
        />
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import PricePopup from '@/components/price-popup/price-popup.vue'

// 金额相关数据
const showPricePopup = ref(false)
const price = ref(80)
const priceType = ref<'hour' | 'times' | 'range'>('hour')
const minPrice = ref(150)
const maxPrice = ref(210)

// 点击金额区域打开弹窗
const handlePriceClick = () => {
    showPricePopup.value = true
}

// 金额确认
const handlePriceConfirm = () => {
    console.log('金额确认:', {
        price: price.value,
        priceType: priceType.value,
        minPrice: minPrice.value,
        maxPrice: maxPrice.value
    })
}

// 发布需求
const handlePublish = () => {
    // TODO: 表单验证和提交
    uni.navigateTo({
        url: '/pages/publish-success/publish-success'
    })
}
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

    .type-hint {
        font-size: 29rpx;
        color: #DADADA;
        margin-left: auto;
    }

    .type-checkbox {
        width: 29rpx;
        height: 29rpx;
        border: 2rpx solid #DADADA;
        border-radius: 50%;
        margin-left: 4rpx;
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

    .switch-off-inner {
        width: 47rpx;
        height: 47rpx;
        background: #fff;
        border-radius: 50%;
    }
}

.switch-on {
    width: 102rpx;
    height: 55rpx;
    background: #00B6B4;
    border-radius: 40rpx;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 4rpx;

    .switch-on-inner {
        width: 47rpx;
        height: 47rpx;
        background: #fff;
        border-radius: 50%;
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

    .publish-btn-text {
        font-size: 29rpx;
        font-weight: 500;
        color: #fff;
    }
}
</style>