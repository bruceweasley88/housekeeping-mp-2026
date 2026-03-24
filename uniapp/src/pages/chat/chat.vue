<template>
	<view class="chat-page">
		<!-- 聊天内容区 -->
		<scroll-view
			class="chat-content"
			scroll-y
			:scroll-top="scrollTop"
			@scrolltoupper="handleScrollToUpper"
		>
			<!-- 加载更多提示 -->
			<view class="loading-more" v-if="loadingMore">
				<u-loading mode="circle" size="40"></u-loading>
			</view>

			<!-- 消息列表 -->
			<template v-for="(msg, index) in messages" :key="msg.id">
				<!-- 时间分隔（如果需要显示） -->
				<view v-if="shouldShowTime(index)" class="time-divider">
					<text class="time-text">{{ msg.create_time_text }}</text>
				</view>

				<!-- 消息气泡 -->
				<chat-bubble
					:position="msg.is_self ? 'right' : 'left'"
					:avatar="msg.avatar"
					:content="msg.content"
				/>
			</template>
		</scroll-view>

		<!-- 底部输入区 -->
		<view class="input-bar">
			<input
				class="message-input"
				type="text"
				placeholder="请输入消息内容…"
				placeholder-class="input-placeholder"
				v-model="inputText"
				@confirm="handleSend"
			/>
			<view class="send-btn" :class="{ 'send-btn-active': inputText.trim() }" @click="handleSend">
				<text class="send-text">发送</text>
			</view>
		</view>
	</view>
</template>

<script setup>
import { ref, nextTick, onUnmounted } from 'vue'
import { onLoad, onShow, onHide } from '@dcloudio/uni-app'
import { getChatMessageList, sendChatMessage, markChatMessageRead } from '@/api/chat'

// 会话ID
const sessionId = ref(0)
// 对方用户信息
const peerUser = ref(null)
// 消息列表
const messages = ref([])
// 输入框内容
const inputText = ref('')
// 滚动位置
const scrollTop = ref(0)
// 上次获取的最后消息ID（用于增量获取）
const lastMessageId = ref(0)
// 是否有更多历史消息
const hasMore = ref(true)
// 当前页码
const currentPage = ref(1)
// 是否正在加载
const loading = ref(false)
// 是否正在加载更多
const loadingMore = ref(false)
// 轮询定时器
let pollingTimer = null

// 页面加载
onLoad((options) => {
	if (options.session_id) {
		sessionId.value = parseInt(options.session_id)
	}
	if (options.peer_user) {
		try {
			peerUser.value = JSON.parse(decodeURIComponent(options.peer_user))
			// 设置导航栏标题为对方昵称
			if (peerUser.value?.nickname) {
				uni.setNavigationBarTitle({
					title: peerUser.value.nickname
				})
			}
		} catch (e) {
			console.error('解析对方用户信息失败', e)
		}
	}
})

// 页面显示
onShow(() => {
	// 首次加载消息
	loadMessages()
	// 标记已读
	markAsRead()
	// 开始轮询
	startPolling()
})

// 页面隐藏
onHide(() => {
	stopPolling()
})

// 组件卸载
onUnmounted(() => {
	stopPolling()
})

// 加载消息
const loadMessages = async (isLoadMore = false) => {
	if (loading.value) return
	loading.value = true

	try {
		const params = {
			session_id: sessionId.value,
			page_size: 20
		}

		if (isLoadMore) {
			// 加载历史消息
			params.page = currentPage.value + 1
		} else {
			// 首次加载或刷新
			params.page = 1
		}

		const res = await getChatMessageList(params)

		if (res) {
			if (isLoadMore) {
				// 历史消息插入到前面
				messages.value = [...res.list, ...messages.value]
				currentPage.value++
				hasMore.value = res.has_more
			} else {
				// 首次加载
				messages.value = res.list || []
				hasMore.value = res.has_more
				currentPage.value = 1

				// 更新最后消息ID
				if (messages.value.length > 0) {
					lastMessageId.value = messages.value[messages.value.length - 1].id
				}

				// 滚动到底部
				scrollToBottom()
			}
		}
	} catch (e) {
		console.error('加载消息失败', e)
	} finally {
		loading.value = false
		loadingMore.value = false
	}
}

// 增量获取新消息
const fetchNewMessages = async () => {
	try {
		const res = await getChatMessageList({
			session_id: sessionId.value,
			last_id: lastMessageId.value
		})

		if (res && res.list && res.list.length > 0) {
			// 添加新消息
			messages.value = [...messages.value, ...res.list]
			// 更新最后消息ID
			lastMessageId.value = res.list[res.list.length - 1].id
			// 滚动到底部
			scrollToBottom()
		}
	} catch (e) {
		console.error('获取新消息失败', e)
	}
}

// 标记已读
const markAsRead = async () => {
	try {
		await markChatMessageRead(sessionId.value)
	} catch (e) {
		console.error('标记已读失败', e)
	}
}

// 发送消息
const handleSend = async () => {
	const content = inputText.value.trim()
	if (!content) return

	inputText.value = ''

	try {
		const res = await sendChatMessage(sessionId.value, content)
		if (res) {
			// 立即获取新消息
			await fetchNewMessages()
		}
	} catch (e) {
		uni.$u.toast('发送失败')
		console.error('发送消息失败', e)
	}
}

// 滚动到底部
const scrollToBottom = () => {
	nextTick(() => {
		scrollTop.value = 999999
	})
}

// 滚动到顶部（加载历史消息）
const handleScrollToUpper = () => {
	if (hasMore.value && !loadingMore.value) {
		loadingMore.value = true
		loadMessages(true)
	}
}

// 开始轮询
const startPolling = () => {
	stopPolling()
	pollingTimer = setInterval(() => {
		fetchNewMessages()
	}, 1000)
}

// 停止轮询
const stopPolling = () => {
	if (pollingTimer) {
		clearInterval(pollingTimer)
		pollingTimer = null
	}
}

// 判断是否显示时间分隔
const shouldShowTime = (index) => {
	if (index === 0) return true

	const currentMsg = messages.value[index]
	const prevMsg = messages.value[index - 1]

	// 如果两条消息间隔超过5分钟，显示时间
	const diff = currentMsg.create_time - prevMsg.create_time
	return diff > 300 // 5分钟
}
</script>

<style lang="scss" scoped>
.chat-page {
	display: flex;
	flex-direction: column;
	height: 100vh;
	background-color: #F1F1F1;
}

// 聊天内容区
.chat-content {
	flex: 1;
	padding-top: 29rpx;
	padding-bottom: 32rpx;
}

// 加载更多
.loading-more {
	display: flex;
	justify-content: center;
	padding: 20rpx 0;
}

// 时间分隔
.time-divider {
	display: flex;
	justify-content: center;
	padding: 29rpx 0;
}

.time-text {
	font-size: 25rpx;
	color: #999999;
	line-height: 25rpx;
}

// 底部输入区
.input-bar {
	display: flex;
	align-items: center;
	padding: 22rpx 29rpx;
	background-color: #F6F6F6;
	padding-bottom: calc(22rpx + env(safe-area-inset-bottom));
}

.message-input {
	flex: 1;
	height: 76rpx;
	background-color: #FFFFFF;
	border-radius: 40rpx;
	padding: 0 25rpx;
	font-size: 29rpx;
}

.input-placeholder {
	color: #999999;
}

.send-btn {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 135rpx;
	height: 76rpx;
	background-color: #DADADA;
	border-radius: 40rpx;
	margin-left: 22rpx;
}

.send-btn-active {
	background-color: #00BAB8;
}

.send-text {
	font-size: 29rpx;
	color: #FFFFFF;
}
</style>
