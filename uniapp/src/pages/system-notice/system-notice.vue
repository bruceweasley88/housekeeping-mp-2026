<template>
	<view class="notice-page">
		<view class="notice-list">
			<!-- 公告项 -->
			<view class="notice-item" v-for="item in noticeList" :key="item.id">
				<text class="notice-time">{{ item.time }}</text>
				<view class="notice-card" @click="goToDetail(item)">
					<text class="notice-title">{{ item.title }}</text>
					<text class="notice-content">{{ item.content }}</text>
					<view class="notice-detail">
						<text class="detail-text">查看详情</text>
						<image class="arrow-icon" src="./assets/img/icon_arrow.png" mode="aspectFit" />
					</view>
				</view>
			</view>

			<!-- 空状态 -->
			<view v-if="noticeList.length === 0" class="empty-state">
				<text class="empty-text">暂无系统公告</text>
			</view>
		</view>
	</view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import { getNoticeLists } from '@/api/notice'

interface NoticeItem {
	id: number
	time: string
	title: string
	content: string
	is_read: number
	related_type: string
	related_id: number
}

// 公告列表
const noticeList = ref<NoticeItem[]>([])

// 加载数据
const loadData = async () => {
	try {
		const res = await getNoticeLists()
		noticeList.value = res || []
	} catch (e) {
		console.error('获取公告列表失败', e)
	}
}

// 页面显示时加载数据
onShow(() => {
	loadData()
})

// 查看详情
const goToDetail = (item: NoticeItem) => {
	uni.navigateTo({
		url: `/pages/notice-detail/notice-detail?id=${item.id}`
	})
}
</script>

<style lang="scss" scoped>
.notice-page {
	min-height: 100vh;
	background-color: #F6F6F6;
}

.notice-list {
	padding: 0 29rpx;
}

.notice-item {
	margin-bottom: 20rpx;
}

.notice-time {
	display: block;
	text-align: center;
	font-size: 26rpx;
	color: #999999;
	margin: 29rpx 0 22rpx;
}

.notice-card {
	background-color: #FFFFFF;
	border-radius: 29rpx;
	padding: 29rpx;
}

.notice-title {
	display: block;
	font-size: 31rpx;
	font-weight: 500;
	color: #222929;
	line-height: 31rpx;
	margin-bottom: 12rpx;
}

.notice-content {
	display: block;
	font-size: 26rpx;
	color: #999999;
	line-height: 36rpx;
}

.notice-detail {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-top: 28rpx;
}

.detail-text {
	font-size: 26rpx;
	color: #222929;
}

.arrow-icon {
	width: 9rpx;
	height: 15rpx;
}

.empty-state {
	padding: 200rpx 0;
	text-align: center;
}

.empty-text {
	font-size: 28rpx;
	color: #999999;
}
</style>
