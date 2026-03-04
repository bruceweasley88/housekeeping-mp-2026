<template>
	<view class="message-page">
		<!-- 消息列表 -->
		<view class="message-list">
			<!-- 系统公告 -->
			<view class="message-item" @click="handleItemClick('system')">
				<view class="avatar-wrap system-avatar">
					<image class="avatar-icon" src="./assets/img/icon_system.png" mode="aspectFit" />
				</view>
				<view class="message-info">
					<text class="message-name">系统公告</text>
					<text class="message-preview">暂无消息</text>
				</view>
				<image class="arrow-icon" src="./assets/img/icon_arrow.png" mode="aspectFit" />
			</view>

			<!-- 官方客服 -->
			<view class="message-item" @click="handleItemClick('service')">
				<view class="avatar-wrap service-avatar">
					<image class="avatar-icon" src="./assets/img/icon_service.png" mode="aspectFit" />
				</view>
				<view class="message-info">
					<text class="message-name">官方客服</text>
					<text class="message-preview has-new">有新的消息</text>
				</view>
				<image class="arrow-icon" src="./assets/img/icon_arrow.png" mode="aspectFit" />
			</view>

			<!-- 用户消息列表 -->
			<view class="message-item" v-for="(item, index) in userMessages" :key="index" @click="handleUserClick(item)">
				<image class="user-avatar" :src="item.avatar" mode="aspectFill" />
				<view class="message-info">
					<view class="name-row">
						<text class="message-name">{{ item.name }}</text>
						<view class="verified-tag" v-if="item.verified">
							<text class="verified-text">已认证</text>
						</view>
					</view>
					<text class="message-preview">{{ item.preview }}</text>
				</view>
				<view class="message-right">
					<text class="message-time">{{ item.time }}</text>
					<view class="unread-badge" v-if="item.unread > 0">
						<text class="unread-text">{{ item.unread > 99 ? '99+' : item.unread }}</text>
					</view>
				</view>
			</view>
		</view>

		<!-- 底部Tabbar占位 -->
		<view class="tabbar-placeholder"></view>
	</view>
</template>

<script setup>
import { ref } from 'vue'

// 用户消息列表数据
const userMessages = ref([
	{
		id: 1,
		name: '小伟妈妈',
		avatar: './assets/img/avatar_1.png',
		preview: '你好，我这边看到你接了我的需求',
		time: '11:20',
		verified: true,
		unread: 6
	},
	{
		id: 2,
		name: '鱼微微',
		avatar: './assets/img/avatar_2.png',
		preview: '我这边大概19:00上门辅导功课，您看可以…',
		time: '11:20',
		verified: true,
		unread: 2
	}
])

// 点击系统消息/客服
const handleItemClick = (type) => {
	uni.navigateTo({
		url: `/pages/${type}-notice/${type}-notice`
	})
}

// 点击用户消息
const handleUserClick = (item) => {
	uni.navigateTo({
		url: `/pages/chat/chat?userId=${item.id}`
	})
}
</script>

<style lang="scss" scoped>
.message-page {
	min-height: 100vh;
	background-color: #F6F6F6;
}

// 消息列表
.message-list {
	background-color: #ffffff;
	margin-top: 16rpx;
}

.message-item {
	display: flex;
	align-items: center;
	padding: 32rpx 32rpx;
	background-color: #ffffff;
	border-bottom: 1rpx solid #F5F5F5;

	&:last-child {
		border-bottom: none;
	}
}

// 头像容器
.avatar-wrap {
	width: 100rpx;
	height: 100rpx;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-shrink: 0;
}

.system-avatar {
	background-color: #E4F9F9;
}

.service-avatar {
	background-color: #E4F9F9;
}

.avatar-icon {
	width: 48rpx;
	height: 58rpx;
}

.service-avatar .avatar-icon {
	width: 62rpx;
	height: 52rpx;
}

// 用户头像
.user-avatar {
	width: 100rpx;
	height: 100rpx;
	border-radius: 50%;
	flex-shrink: 0;
}

// 消息信息
.message-info {
	flex: 1;
	margin-left: 20rpx;
	overflow: hidden;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.name-row {
	display: flex;
	align-items: center;
	gap: 12rpx;
}

.message-name {
	font-size: 34rpx;
	font-weight: 500;
	color: #222222;
}

.message-preview {
	font-size: 28rpx;
	color: #999999;
	margin-top: 4rpx;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;

	&.has-new {
		color: #00A2A0;
	}
}

// 认证标签
.verified-tag {
	background-color: #FF7E22;
	border-radius: 6rpx;
	padding: 2rpx 8rpx;
}

.verified-text {
	font-size: 22rpx;
	color: #ffffff;
}

// 箭头图标
.arrow-icon {
	width: 10rpx;
	height: 16rpx;
	flex-shrink: 0;
}

// 右侧信息
.message-right {
	display: flex;
	flex-direction: column;
	align-items: flex-end;
	margin-left: 20rpx;
	flex-shrink: 0;
}

.message-time {
	font-size: 28rpx;
	color: #999999;
}

// 未读消息角标
.unread-badge {
	min-width: 36rpx;
	height: 36rpx;
	border-radius: 18rpx;
	background-color: #FF3D47;
	display: flex;
	align-items: center;
	justify-content: center;
	margin-top: 6rpx;
	padding: 0 8rpx;
}

.unread-text {
	font-size: 22rpx;
	font-weight: 700;
	color: #ffffff;
}

// 底部Tabbar占位
.tabbar-placeholder {
	height: 156rpx;
}
</style>
