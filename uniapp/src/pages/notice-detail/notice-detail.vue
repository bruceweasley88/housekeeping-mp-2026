<template>
	<view class="notice-detail-page">
		<view class="detail-content">
			<text class="notice-time">{{ noticeData.time }}</text>
			<text class="notice-title">{{ noticeData.title }}</text>
			<text class="notice-content">{{ noticeData.content }}</text>
		</view>
	</view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { getNoticeDetail } from '@/api/notice'

interface NoticeDetail {
	id: number
	time: string
	title: string
	content: string
	related_type: string
	related_id: number
}

const noticeData = ref<NoticeDetail>({
	id: 0,
	time: '',
	title: '',
	content: '',
	related_type: '',
	related_id: 0
})

// 加载详情
const loadDetail = async (id: number) => {
	try {
		const res = await getNoticeDetail(id)
		noticeData.value = {
			id: res.id,
			time: res.time,
			title: res.title,
			content: res.content,
			related_type: res.related_type,
			related_id: res.related_id
		}
	} catch (e) {
		console.error('获取公告详情失败', e)
	}
}

onLoad((options) => {
	const id = options?.id
	if (id) {
		loadDetail(Number(id))
	}
})
</script>

<style lang="scss" scoped>
.notice-detail-page {
	min-height: 100vh;
	background-color: #FFFFFF;
}

.detail-content {
	padding: 29rpx;
}

.notice-time {
	display: block;
	text-align: left;
	font-size: 26rpx;
	color: #999999;
	line-height: 26rpx;
}

.notice-title {
	display: block;
	font-size: 31rpx;
	font-weight: 500;
	color: #222929;
	line-height: 31rpx;
	margin-top: 22rpx;
}

.notice-content {
	display: block;
	font-size: 26rpx;
	color: #999999;
	line-height: 36rpx;
	margin-top: 11rpx;
}
</style>
