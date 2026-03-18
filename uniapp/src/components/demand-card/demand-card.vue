<template>
    <view class="demand-card" @click="handleCardClick">
        <!-- 顶部：标签 + 标题 + 地址 -->
        <view class="card-header">
            <view class="tag" v-if="showUrgentTag">紧急</view>
            <text class="title">{{ title }}</text>
            <view class="location" @click="handleLocationClick">
                <text class="location-text">{{ location }}</text>
                <image class="arrow-icon" src="/static/index_page/icon_arrow_right.png" mode="aspectFit"/>
            </view>
        </view>

        <!-- 中间：描述 + 价格 + 图片 -->
        <view class="card-content">
            <view class="content-left">
                <text class="description">{{ description }}</text>
                <view class="price-row">
                    <text class="price">{{ computedPrice.value }}</text>
                    <text class="price-unit">{{ computedPrice.unit }}</text>
                </view>
            </view>
            <image
                v-if="image"
                class="content-image"
                :src="image"
                mode="aspectFill"
            />
        </view>

        <!-- 底部：用户信息 + 按钮 -->
        <view class="card-footer">
            <view class="user-info">
                <image class="avatar" :src="avatar || '/static/index_page/icon_avatar_default.png'" mode="aspectFill"/>
                <view class="user-text">
                    <text class="username">{{ username }}</text>
                    <text class="publish-time">{{ formatPublishTime(createTime) }}</text>
                </view>
            </view>
            <view class="action-btn" @click="handleAction">{{ actionText }}</view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
    // 是否紧急
    isUrgent: {
        type: [Number, Boolean],
        default: 0
    },
    // 标题
    title: {
        type: String,
        default: ''
    },
    // 地址
    location: {
        type: String,
        default: ''
    },
    // 描述
    description: {
        type: String,
        default: ''
    },
    // 价格类型：1=按小时，2=按次，3=按范围
    priceType: {
        type: Number,
        default: 0  // 0 表示使用旧的 price/priceUnit 方式
    },
    // 按次金额
    amount: {
        type: [Number, String],
        default: 0
    },
    // 按小时 - 每小时金额
    hourPrice: {
        type: [Number, String],
        default: 0
    },
    // 按范围 - 最小金额
    minAmount: {
        type: [Number, String],
        default: 0
    },
    // 按范围 - 最大金额
    maxAmount: {
        type: [Number, String],
        default: 0
    },
    // 价格（旧方式，兼容用）
    price: {
        type: String,
        default: ''
    },
    // 价格单位（旧方式，兼容用）
    priceUnit: {
        type: String,
        default: ''
    },
    // 图片
    image: {
        type: String,
        default: ''
    },
    // 用户头像
    avatar: {
        type: String,
        default: ''
    },
    // 用户名
    username: {
        type: String,
        default: ''
    },
    // 发布时间
    createTime: {
        type: [Number, String],
        default: ''
    },
    // 按钮文字
    actionText: {
        type: String,
        default: '接取任务'
    }
})

// 计算价格显示
const computedPrice = computed(() => {
    // 如果 priceType 为 0，使用旧的 price/priceUnit 方式
    if (!props.priceType) {
        return {
            value: props.price,
            unit: props.priceUnit
        }
    }

    const type = props.priceType

    if (type === 1) {
        // 按小时
        return {
            value: String(props.hourPrice || 0),
            unit: '/小时'
        }
    } else if (type === 3) {
        // 按范围：整体显示 "min~max元"
        return {
            value: (props.minAmount || 0) + '~' + (props.maxAmount || 0) + '元',
            unit: ''
        }
    } else {
        // 按次
        return {
            value: String(props.amount || 0),
            unit: '/次'
        }
    }
})

// 是否显示紧急标签
const showUrgentTag = computed(() => {
    return props.isUrgent === 1 || props.isUrgent === true
})

// 格式化发布时间
const formatPublishTime = (time: number | string): string => {
    if (!time) return ''
    let date: Date
    if (typeof time === 'number') {
        // Unix 时间戳（秒）
        date = new Date(time * 1000)
    } else if (typeof time === 'string') {
        // 字符串格式 "2026-03-10 10:00:00" 或 "2026/03/10T10:00:00"
        const dateStr = time.replace(/-/g, '/')
        date = new Date(dateStr)
    } else {
        date = new Date(time)
    }
    if (isNaN(date.getTime())) return ''
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    const hour = String(date.getHours()).padStart(2, '0')
    const minute = String(date.getMinutes()).padStart(2, '0')
    return `发布于${year}.${month}.${day} ${hour}:${minute}`
}

const emit = defineEmits(['action', 'location', 'cardClick'])

const handleCardClick = () => {
    emit('cardClick')
}

const handleAction = () => {
    emit('action')
}

const handleLocationClick = () => {
    emit('location')
}
</script>

<style lang="scss" scoped>
.demand-card {
    background: #fff;
    border-radius: 29rpx;
    padding: 29rpx 31rpx 22rpx 31rpx;
    margin-bottom: 22rpx;
}

.card-header {
    display: flex;
    align-items: center;
    margin-bottom: 18rpx;

    .tag {
        background: #F04530;
        border-radius: 10rpx;
        padding: 4rpx 10rpx;
        font-size: 25rpx;
        color: #fff;
        margin-right: 8rpx;
        flex-shrink: 0;
    }

    .title {
        font-size: 33rpx;
        font-weight: 500;
        color: #222929;
        flex-shrink: 0;
        max-width: 330rpx;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .location {
        display: flex;
        align-items: center;
        margin-left: auto;

        .location-text {
            font-size: 25rpx;
            color: #9CA6A6;
            max-width: 180rpx;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .arrow-icon {
            width: 9rpx;
            height: 15rpx;
            margin-left: 11rpx;
        }
    }
}

.card-content {
    display: flex;
    justify-content: space-between;
    margin-bottom: 31rpx;

    .content-left {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-right: 20rpx;

        .description {
            font-size: 25rpx;
            color: #616B6B;
            line-height: 36rpx;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }

        .price-row {
            display: flex;
            align-items: flex-end;
            margin-top: 7rpx;

            .price {
                font-size: 44rpx;
                font-weight: 700;
                color: #F04530;
            }

            .price-unit {
                font-size: 25rpx;
                font-weight: 700;
                color: #F04530;
                margin-left: 5rpx;
                margin-bottom: 6rpx;
            }
        }
    }

    .content-image {
        width: 157rpx;
        height: 116rpx;
        border-radius: 12rpx;
        flex-shrink: 0;
    }
}

.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;

    .user-info {
        display: flex;
        align-items: center;

        .avatar {
            width: 62rpx;
            height: 62rpx;
            border-radius: 50%;
            margin-right: 13rpx;
        }

        .user-text {
            display: flex;
            flex-direction: column;

            .username {
                font-size: 25rpx;
                color: #222929;
            }

            .publish-time {
                font-size: 22rpx;
                color: #9CA6A6;
                margin-top: 2rpx;
            }
        }
    }

    .action-btn {
        background: #00B6B4;
        border-radius: 60rpx;
        padding: 0 33rpx;
        height: 65rpx;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24rpx;
        color: #fff;
    }
}
</style>