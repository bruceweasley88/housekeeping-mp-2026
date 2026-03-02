<template>
    <view class="demand-card">
        <!-- 顶部：标签 + 标题 + 地址 -->
        <view class="card-header">
            <view class="tag" v-if="tag">{{ tag }}</view>
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
                    <text class="price">{{ price }}</text>
                    <text class="price-unit">{{ priceUnit }}</text>
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
                    <text class="publish-time">{{ publishTime }}</text>
                </view>
            </view>
            <view class="action-btn" @click="handleAction">{{ actionText }}</view>
        </view>
    </view>
</template>

<script setup lang="ts">
defineProps({
    // 标签（如：紧急）
    tag: {
        type: String,
        default: ''
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
    // 价格
    price: {
        type: String,
        default: ''
    },
    // 价格单位
    priceUnit: {
        type: String,
        default: '元'
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
    publishTime: {
        type: String,
        default: ''
    },
    // 按钮文字
    actionText: {
        type: String,
        default: '接取任务'
    }
})

const emit = defineEmits(['action', 'location'])

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
        border-radius: 5rpx;
        padding: 4rpx 7rpx;
        font-size: 25rpx;
        color: #fff;
        margin-right: 5rpx;
        flex-shrink: 0;
    }

    .title {
        font-size: 33rpx;
        font-weight: 500;
        color: #222929;
        flex-shrink: 0;
        max-width: 280rpx;
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