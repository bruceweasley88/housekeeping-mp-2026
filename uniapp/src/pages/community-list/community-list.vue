<template>
    <view class="page">
        <!-- 省市区选择 -->
        <view class="region-selector" @click="showRegionPicker = true">
            <text class="region-text" :class="{ 'has-value': regionText }">
                {{ regionText || '请选择省市区' }}
            </text>
            <u-icon name="arrow-down" size="20" color="#9CA6A6" />
        </view>

        <!-- 搜索框 -->
        <view class="search-box">
            <view class="search-input-wrapper">
                <u-icon name="search" size="28" color="#9CA6A6" />
                <input
                    class="search-input"
                    v-model="keyword"
                    placeholder="搜索小区名称"
                    placeholder-style="color: #DADADA; font-size: 28rpx;"
                    @confirm="handleSearch"
                />
                <u-icon
                    v-if="keyword"
                    name="close-circle-fill"
                    size="32"
                    color="#DADADA"
                    @click="clearKeyword"
                />
            </view>
        </view>

        <!-- 附近小区标题 -->
        <view class="location-header">
            <image class="location-icon" src="/static/images/community/icon_location.png" mode="aspectFit"/>
            <text class="location-text">小区列表</text>
            <text class="count-text" v-if="communityList.length">({{ communityList.length }})</text>
        </view>

        <!-- 小区列表 -->
        <scroll-view class="community-list" scroll-y>
            <!-- 加载中 -->
            <view class="loading-wrapper" v-if="loading">
                <u-loadmore status="loading" />
            </view>

            <!-- 空状态 -->
            <view class="empty-wrapper" v-else-if="!communityList.length">
                <text class="empty-text">暂无小区数据</text>
                <text class="empty-tip">请选择其他地区或申请新增小区</text>
            </view>

            <!-- 列表 -->
            <view v-else>
                <view
                    class="community-item"
                    :class="{ 'selected': selectedIndex === index }"
                    v-for="(item, index) in communityList"
                    :key="item.id"
                    @click="handleSelect(index)"
                >
                    <view class="community-info">
                        <text class="community-name">{{ item.name }}</text>
                        <text class="community-address">地址：{{ item.address }}</text>
                    </view>
                    <view class="check-icon" v-if="selectedIndex === index">
                        <text class="check-mark">✓</text>
                    </view>
                </view>
            </view>
        </scroll-view>

        <!-- 新增小区入口 -->
        <view class="add-community-btn" @click="handleAddCommunity">
            <text class="add-text">+ 新增小区</text>
        </view>

        <!-- 确定按钮 -->
        <view class="confirm-btn" @click="handleConfirm">
            <text class="confirm-text">确定</text>
        </view>

        <!-- 省市区选择器 -->
        <u-picker
            mode="region"
            v-model="showRegionPicker"
            confirm-color="#00B6B4"
            :default-region="defaultRegion"
            @confirm="onRegionConfirm"
        />
    </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { getCommunityLists, searchCommunity, getUserAddress } from '@/api/community'

// 省市区数据
const province = ref('')
const city = ref('')
const district = ref('')
const showRegionPicker = ref(false)

// 显示的省市区文本
const regionText = computed(() => {
    if (province.value && city.value && district.value) {
        return `${province.value} ${city.value} ${district.value}`
    }
    return ''
})

// 默认选中的省市区（用于 picker 回显）
const defaultRegion = computed(() => {
    if (province.value && city.value && district.value) {
        return [province.value, city.value, district.value]
    }
    return []
})

// 搜索关键词
const keyword = ref('')

// 小区列表数据
const communityList = ref<any[]>([])
const loading = ref(false)

// 当前选中索引
const selectedIndex = ref(-1)

// 用户当前小区ID
const currentCommunityId = ref(0)

// 获取小区列表
const fetchList = async () => {
    if (!province.value || !city.value || !district.value) {
        return
    }

    loading.value = true
    try {
        const params = {
            province: province.value,
            city: city.value,
            district: district.value,
            page: 1,
            size: 50
        }

        let res
        if (keyword.value) {
            res = await searchCommunity({ ...params, keyword: keyword.value })
        } else {
            res = await getCommunityLists(params)
        }

        communityList.value = res.list || []
        selectedIndex.value = -1
    } catch (e) {
        communityList.value = []
    } finally {
        loading.value = false
    }
}

// 省市区选择确认
const onRegionConfirm = (e: any) => {
    province.value = e.province?.name || ''
    city.value = e.city?.name || ''
    district.value = e.area?.name || ''
    showRegionPicker.value = false
    fetchList()
}

// 搜索
const handleSearch = () => {
    fetchList()
}

// 清空搜索关键词
const clearKeyword = () => {
    keyword.value = ''
    fetchList()
}

// 选择小区
const handleSelect = (index: number) => {
    selectedIndex.value = index
}

// 确认选择
const handleConfirm = () => {
    if (selectedIndex.value < 0) {
        uni.showToast({
            title: '请选择小区',
            icon: 'none'
        })
        return
    }

    const selectedCommunity = communityList.value[selectedIndex.value]
    // 返回上一页并传递选中的小区
    uni.$emit('community-selected', selectedCommunity)
    uni.navigateBack()
}

// 跳转新增小区页面
const handleAddCommunity = () => {
    uni.navigateTo({
        url: '/pages/add-community/add-community'
    })
}

// 页面加载时检查用户是否已有小区
onMounted(async () => {
    try {
        const res = await getUserAddress()
        if (res && res.community_id) {
            // 用户已有小区，自动定位
            province.value = res.province || ''
            city.value = res.city || ''
            district.value = res.district || ''
            currentCommunityId.value = res.community_id
            // 加载列表
            await fetchList()
            // 在列表中找到并选中当前小区
            const index = communityList.value.findIndex(item => item.id === currentCommunityId.value)
            if (index >= 0) {
                selectedIndex.value = index
            }
        } else {
            // 用户没有小区，显示选择器
            showRegionPicker.value = true
        }
    } catch (e) {
        // 获取失败（未登录等），显示选择器
        showRegionPicker.value = true
    }
})
</script>

<style lang="scss" scoped>
.page {
    min-height: 100vh;
    background: #fff;
    display: flex;
    flex-direction: column;
}

// 省市区选择
.region-selector {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24rpx 36rpx;
    background: #F6F6F6;

    .region-text {
        font-size: 28rpx;
        color: #9CA6A6;
        margin-right: 8rpx;

        &.has-value {
            color: #222929;
        }
    }
}

// 搜索框
.search-box {
    padding: 20rpx 36rpx;
    background: #fff;

    .search-input-wrapper {
        display: flex;
        align-items: center;
        height: 72rpx;
        padding: 0 24rpx;
        background: #F6F6F6;
        border-radius: 36rpx;
    }

    .search-input {
        flex: 1;
        height: 72rpx;
        margin: 0 16rpx;
        font-size: 28rpx;
        color: #222929;
    }
}

// 附近小区标题
.location-header {
    display: flex;
    align-items: center;
    padding: 30rpx 36rpx;

    .location-icon {
        width: 31rpx;
        height: 31rpx;
        margin-right: 16rpx;
    }

    .location-text {
        font-size: 29rpx;
        font-weight: 500;
        color: #222929;
    }

    .count-text {
        font-size: 24rpx;
        color: #9CA6A6;
        margin-left: 8rpx;
    }
}

// 小区列表
.community-list {
    flex: 1;
}

.loading-wrapper {
    padding: 60rpx 0;
}

.empty-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 100rpx 0;

    .empty-text {
        font-size: 28rpx;
        color: #9CA6A6;
        margin-bottom: 16rpx;
    }

    .empty-tip {
        font-size: 24rpx;
        color: #DADADA;
    }
}

.community-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 32rpx 36rpx;
    border-bottom: 2rpx solid #F5F5F5;

    &.selected {
        .community-name {
            color: #00B6B4;
        }
    }
}

.community-info {
    flex: 1;
}

.community-name {
    font-size: 33rpx;
    font-weight: 500;
    color: #222929;
    display: block;
    margin-bottom: 8rpx;
}

.community-address {
    font-size: 25rpx;
    color: #9CA6A6;
}

.check-icon {
    width: 44rpx;
    height: 44rpx;
    background: #00B6B4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.check-mark {
    font-size: 24rpx;
    color: #fff;
}

// 新增小区按钮
.add-community-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30rpx 36rpx;

    .add-text {
        font-size: 28rpx;
        color: #00B6B4;
    }
}

// 确定按钮
.confirm-btn {
    margin: 0 30rpx calc(30rpx + env(safe-area-inset-bottom));
    height: 102rpx;
    background: #00B6B4;
    border-radius: 51rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

.confirm-text {
    font-size: 36rpx;
    font-weight: 500;
    color: #fff;
}
</style>
