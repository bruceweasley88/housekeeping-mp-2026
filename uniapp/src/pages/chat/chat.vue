<template>
	<view class="chat-page">
		<!-- 聊天内容区 -->
		<scroll-view class="chat-content" scroll-y :scroll-top="scrollTop">
			<!-- 时间分隔 -->
			<view class="time-divider">
				<text class="time-text">2026.01.04 15:32</text>
			</view>

			<!-- 消息列表 -->
			<template v-for="(msg, index) in messages" :key="index">
				<!-- 时间分隔（如果需要显示） -->
				<view v-if="msg.showTime" class="time-divider">
					<text class="time-text">{{ msg.time }}</text>
				</view>

				<!-- 消息气泡 -->
				<chat-bubble
					:position="msg.position"
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
			/>
			<view class="send-btn" :class="{ 'send-btn-active': inputText }">
				<text class="send-text">发送</text>
			</view>
		</view>
	</view>
</template>

<script setup>
import { ref } from 'vue'

// 输入框内容
const inputText = ref('')

// 滚动位置
const scrollTop = ref(0)

// 模拟消息数据
const messages = ref([
	{
		position: 'left',
		avatar: './assets/img/avatar_other.png',
		content: '你好',
		time: '',
		showTime: false
	},
	{
		position: 'right',
		avatar: './assets/img/avatar_self.png',
		content: '你好',
		time: '',
		showTime: false
	},
	{
		position: 'left',
		avatar: './assets/img/avatar_other.png',
		content: '看到你接了我的需求，我这边说一下具体要求哦～',
		time: '',
		showTime: false
	},
	{
		position: 'left',
		avatar: './assets/img/avatar_other.png',
		content: '需求的工作时间是2小时，在每周六日晚上19:00，帮忙辅导我家小孩数学功课，他现在初中三年级。',
		time: '',
		showTime: false
	},
	{
		position: 'left',
		avatar: './assets/img/avatar_other.png',
		content: '这边大概1小时200元，当天补习当天日结，你看这边有没有其他问题？',
		time: '',
		showTime: false
	},
	{
		position: 'right',
		avatar: './assets/img/avatar_self.png',
		content: '没问题，请问具体栋数和门牌号是？我这边会按时上门',
		time: '',
		showTime: false
	}
])
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
