<template>
	<view class="chat-bubble" :class="positionClass">
		<!-- 左侧头像（对方消息） -->
		<image
			v-if="position === 'left'"
			class="avatar"
			:src="avatar"
			mode="aspectFill"
		/>

		<!-- 气泡内容 -->
		<view class="bubble" :class="bubbleClass">
			<text class="bubble-text" :class="{ 'text-white': position === 'right' }">{{ content }}</text>
		</view>

		<!-- 右侧头像（自己消息） -->
		<image
			v-if="position === 'right'"
			class="avatar"
			:src="avatar"
			mode="aspectFill"
		/>
	</view>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	// 气泡位置：left-对方消息，right-自己消息
	position: {
		type: String,
		default: 'left',
		validator: (value) => ['left', 'right'].includes(value)
	},
	// 头像URL
	avatar: {
		type: String,
		default: ''
	},
	// 消息内容
	content: {
		type: String,
		default: ''
	}
})

// 动态计算位置类名
const positionClass = computed(() => {
	return props.position === 'right' ? 'bubble-right' : 'bubble-left'
})

// 动态计算气泡类名
const bubbleClass = computed(() => {
	return props.position === 'right' ? 'bubble-green' : 'bubble-white'
})
</script>

<style lang="scss" scoped>
.chat-bubble {
	display: flex;
	align-items: flex-start;
	margin-bottom: 24rpx;
	padding: 0 32rpx;
}

.bubble-left {
	justify-content: flex-start;
}

.bubble-right {
	justify-content: flex-end;
}

// 头像
.avatar {
	width: 76rpx;
	height: 76rpx;
	border-radius: 50%;
	flex-shrink: 0;
}

// 气泡
.bubble {
	max-width: 486rpx;
	padding: 18rpx 28rpx;
	border-radius: 11rpx;
	word-break: break-all;
}

.bubble-white {
	background-color: #FEFEFE;
	margin-left: 16rpx;
}

.bubble-green {
	background-color: #00BAB8;
	margin-right: 16rpx;
}

.bubble-text {
	font-size: 31rpx;
	line-height: 42rpx;
	color: #000000;
}

.text-white {
	color: #FEFEFE;
}
</style>
