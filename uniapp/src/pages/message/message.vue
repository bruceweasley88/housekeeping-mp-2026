<template>
	<view class="message-page">
		<custom-nav-title title="消息" />

		<!-- 消息列表 -->
		<view class="message-list">
			<!-- 系统公告 -->
			<view class="message-item" @click="handleItemClick('system')">
				<view class="avatar-wrap system-avatar">
					<image class="avatar-icon" src="./assets/img/icon_system.png" mode="aspectFit" />
				</view>
				<view class="message-info">
					<text class="message-name">系统公告</text>
					<text class="message-preview" :class="{ 'has-new': latestNotice }">{{ latestNotice || '暂无消息' }}</text>
				</view>
				<view class="message-right">
					<view class="unread-badge" v-if="systemUnreadCount > 0">
						<text class="unread-text">{{ systemUnreadCount > 99 ? '99+' : systemUnreadCount }}</text>
					</view>
					<image class="arrow-icon" src="./assets/img/icon_arrow.png" mode="aspectFit" />
				</view>
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
			<view class="message-item" v-for="(item, index) in chatSessionList" :key="index" @click="handleUserClick(item)">
				<image class="user-avatar" :src="item.peer_user.avatar" mode="aspectFill" />
				<view class="message-info">
					<text class="message-name">{{ item.peer_user.nickname }}</text>
					<text class="message-preview">{{ item.last_message?.content || '暂无消息' }}</text>
				</view>
				<view class="message-right">
					<text class="message-time">{{ formatTime(item.update_time) }}</text>
					<view class="unread-badge" v-if="item.unread_count > 0">
						<text class="unread-text">{{ item.unread_count > 99 ? '99+' : item.unread_count }}</text>
					</view>
				</view>
			</view>
		</view>

		<!-- 底部Tabbar占位 -->
		<view class="tabbar-placeholder"></view>

		<tabbar />
	</view>
</template>

<script setup>
import { ref, onUnmounted } from 'vue'
import { onShow, onHide } from '@dcloudio/uni-app'
import { getNoticeLists, getNoticeUnreadCount } from '@/api/notice'
import { getChatSessionList } from '@/api/chat'
import { goToCustomerService } from '@/utils/util'

// 系统公告未读数量
const systemUnreadCount = ref(0)
// 最新公告预览
const latestNotice = ref('')
// 聊天会话列表
const chatSessionList = ref([])
// 轮询定时器
let pollingTimer = null

// 加载系统公告数据
const loadNoticeData = async () => {
	try {
		// 获取未读数量
		const countRes = await getNoticeUnreadCount()
		systemUnreadCount.value = countRes?.count || 0

		// 获取最新一条公告作为预览
		const listRes = await getNoticeLists()
		if (listRes && listRes.length > 0) {
			latestNotice.value = listRes[0].title
		} else {
			latestNotice.value = ''
		}
	} catch (e) {
		console.error('获取系统公告数据失败', e)
	}
}

// 加载聊天会话列表
const loadChatSessionList = async () => {
	try {
		const res = await getChatSessionList()
		chatSessionList.value = res?.list || []
	} catch (e) {
		console.error('获取聊天会话列表失败', e)
	}
}

// 开始轮询
const startPolling = () => {
	stopPolling()
	loadChatSessionList()
	pollingTimer = setInterval(() => {
		loadChatSessionList()
	}, 1000)
}

// 停止轮询
const stopPolling = () => {
	if (pollingTimer) {
		clearInterval(pollingTimer)
		pollingTimer = null
	}
}

// 格式化时间显示
const formatTime = (timestamp) => {
	if (!timestamp) return ''

	const now = Date.now()
	const diff = Math.floor((now - timestamp * 1000) / 1000)

	// 1分钟内：刚刚
	if (diff < 60) {
		return '刚刚'
	}

	// 1小时内：X分钟前
	if (diff < 3600) {
		return Math.floor(diff / 60) + '分钟前'
	}

	const date = new Date(timestamp * 1000)
	const today = new Date()
	today.setHours(0, 0, 0, 0)

	const yesterday = new Date(today)
	yesterday.setDate(yesterday.getDate() - 1)

	// 今天：HH:mm
	if (date >= today) {
		return formatTimeStr(date, 'H:i')
	}

	// 昨天：昨天
	if (date >= yesterday) {
		return '昨天'
	}

	// 今年：MM-DD
	if (date.getFullYear() === today.getFullYear()) {
		return formatTimeStr(date, 'm-d')
	}

	// 往年：YYYY-MM-DD
	return formatTimeStr(date, 'Y-m-d')
}

// 格式化时间字符串
const formatTimeStr = (date, format) => {
	const pad = (n) => n.toString().padStart(2, '0')

	const replacements = {
		'Y': date.getFullYear(),
		'm': pad(date.getMonth() + 1),
		'd': pad(date.getDate()),
		'H': pad(date.getHours()),
		'i': pad(date.getMinutes()),
	}

	let result = format
	for (const [key, value] of Object.entries(replacements)) {
		result = result.replace(key, value)
	}
	return result
}

// 页面显示时加载数据
onShow(() => {
	loadNoticeData()
	startPolling()
})

// 页面隐藏时停止轮询
onHide(() => {
	stopPolling()
})

// 组件卸载时清理
onUnmounted(() => {
	stopPolling()
})

// 点击系统消息/客服
const handleItemClick = (type) => {
	if (type === 'service') {
		goToCustomerService()
		return
	}
	uni.navigateTo({
		url: `/pages/${type}-notice/${type}-notice`
	})
}

// 点击用户消息
const handleUserClick = (item) => {
	// 跳转到聊天页面，传递session_id和对方用户信息
	const peerUser = encodeURIComponent(JSON.stringify(item.peer_user))
	uni.navigateTo({
		url: `/pages/chat/chat?session_id=${item.session_id}&peer_user=${peerUser}`
	})
}
</script>

<style lang="scss" scoped>
.message-page {
	min-height: 100vh;
	background: linear-gradient(90deg, #C3FBEF 0%, #C0F6FD 100%);
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

// 系统公告的右侧（水平布局：未读角标 + 箭头）
.message-item:first-child .message-right {
	flex-direction: row;
	align-items: center;
	gap: 12rpx;
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
