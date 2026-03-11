<template>
  <view class="all-demands-page">
    <!-- 搜索框区域 -->
    <view class="search-section">
      <view class="search-wrapper">
        <image
          class="search-icon"
          src="/static/images/icon/icon_search.png"
          mode="aspectFit"
        />
        <input
          class="search-input"
          type="text"
          v-model="keyword"
          placeholder="请输入任务关键词"
          placeholder-style="color: #999999;"
          @confirm="handleSearch"
        />
        <view class="search-btn" @click="handleSearch">
          <text class="search-btn-text">搜索</text>
        </view>
      </view>
    </view>

    <!-- 分类标签栏 -->
    <view class="category-section">
      <scroll-view class="category-scroll" scroll-x="true" show-scrollbar="false">
        <view class="category-list">
          <view
            class="category-item"
            :class="{ active: currentCategoryId === item.id }"
            v-for="item in categoryList"
            :key="item.id"
            @click="handleCategoryChange(item.id)"
          >
            <text class="category-text" :class="{ active: currentCategoryId === item.id }">{{ item.name }}</text>
            <view class="category-indicator" v-if="currentCategoryId === item.id"></view>
          </view>
        </view>
      </scroll-view>
    </view>

    <!-- 需求列表 -->
    <view class="demand-list">
      <view v-if="demandList.length === 0 && !loading" class="empty-tip">
        <text>暂无需求</text>
      </view>
      <demand-card
        v-for="item in demandList"
        :key="item.id"
        :tag="item.is_urgent === 1 ? '紧急' : ''"
        :title="item.title"
        :location="item.address"
        :description="item.description"
        :price="getPriceValue(item)"
        :priceUnit="getPriceUnit(item)"
        :image="item.images?.[0] || ''"
        :avatar="item.user_info?.avatar || ''"
        :username="item.user_info?.nickname || ''"
        :publishTime="formatPublishTime(item.create_time)"
        actionText="承接需求"
        @action="handleTakeOrder(item)"
        @location="handleLocationClick(item)"
      />
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { getDemandCategoryLists, getDemandLists } from '@/api/demand'

// 分类列表
const categoryList = ref<{ id: number; name: string; icon: string }[]>([])

// 当前选中的分类ID
const currentCategoryId = ref(0)

// 需求列表
const demandList = ref<any[]>([])

// 搜索关键词
const keyword = ref('')

// 加载状态
const loading = ref(false)

// 加载分类列表
const fetchCategoryList = async () => {
  try {
    const res = await getDemandCategoryLists()
    // 在前面添加"全部需求"静态项
    categoryList.value = [
      { id: 0, name: '全部需求', icon: 'icon_all.png' },
      ...(res || [])
    ]
  } catch (e) {
    categoryList.value = [
      { id: 0, name: '全部需求', icon: 'icon_all.png' }
    ]
  }
}

// 加载需求列表
const fetchDemandList = async () => {
  loading.value = true
  try {
    const params: any = {}
    // 只有非0的分类ID才传递
    if (currentCategoryId.value !== 0) {
      params.category_id = currentCategoryId.value
    }
    // 搜索关键词
    if (keyword.value) {
      params.keyword = keyword.value
    }

    const res = await getDemandLists(params)
    demandList.value = res?.list || []
  } catch (e) {
    demandList.value = []
  } finally {
    loading.value = false
  }
}

// 分类切换
const handleCategoryChange = (categoryId: number) => {
  if (currentCategoryId.value === categoryId) return
  currentCategoryId.value = categoryId
  fetchDemandList()
}

// 搜索
const handleSearch = () => {
  currentCategoryId.value = 0  // 重置分类为"全部"
  fetchDemandList()
}

// 承接需求按钮点击
const handleTakeOrder = (item: any) => {
  uni.navigateTo({
    url: `/pages/demand/demand?id=${item.id}`
  })
}

// 地址点击
const handleLocationClick = (item: any) => {
  uni.showToast({
    title: '查看位置',
    icon: 'none'
  })
}

// 获取价格显示值
const getPriceValue = (item: any): string => {
  const priceType = item.price_type
  if (priceType === 1) {
    // 按小时
    return String(item.hour_price || 0)
  } else if (priceType === 3) {
    // 按范围
    return String(item.min_amount || 0)
  } else {
    // 按次
    return String(item.amount || 0)
  }
}

// 获取价格单位
const getPriceUnit = (item: any): string => {
  const priceType = item.price_type
  if (priceType === 1) {
    // 按小时
    return '元/小时'
  } else if (priceType === 3) {
    // 按范围
    return '~' + (item.max_amount || 0) + '元'
  } else {
    // 按次
    return '元/次'
  }
}

// 格式化发布时间
const formatPublishTime = (time: number | string): string => {
  if (!time) return ''
  let date: Date
  if (typeof time === 'number') {
    // Unix 时间戳（秒）
    date = new Date(time * 1000)
  } else {
    // 日期字符串
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

// 页面加载
onLoad((options) => {
  // 获取传入的分类ID
  const categoryId = options?.category_id ? parseInt(options.category_id) : 0
  currentCategoryId.value = categoryId

  // 加载分类和需求
  fetchCategoryList()
  fetchDemandList()
})
</script>

<style scoped lang="scss">
.all-demands-page {
  background-color: #F6F6F6;
  min-height: 100vh;
  padding-top: 20rpx;

  // 搜索框区域
  .search-section {
    padding: 0 30rpx 24rpx;

    .search-wrapper {
      background-color: #FFFFFF;
      border-radius: 58rpx;
      height: 87rpx;
      border: 2rpx solid #00B6B4;
      display: flex;
      align-items: center;
      padding: 0 7rpx 0 29rpx;

      .search-icon {
        width: 40rpx;
        height: 40rpx;
        margin-right: 20rpx;
      }

      .search-input {
        flex: 1;
        height: 60rpx;
        font-size: 29rpx;
        color: #222222;
      }

      .search-btn {
        background-color: #00B6B4;
        border-radius: 58rpx;
        height: 73rpx;
        width: 140rpx;
        display: flex;
        align-items: center;
        justify-content: center;

        .search-btn-text {
          font-size: 29rpx;
          color: #FFFFFF;
          font-weight: 500;
        }
      }
    }
  }

  // 分类标签栏
  .category-section {
    margin-bottom: 16rpx;

    .category-scroll {
      white-space: nowrap;

      .category-list {
        display: flex;
        padding: 0 30rpx;

        .category-item {
          position: relative;
          margin-right: 36rpx;
          padding-bottom: 9rpx;

          &.active {
            .category-indicator {
              position: absolute;
              bottom: 0;
              left: 50%;
              transform: translateX(-50%);
              width: 131rpx;
              height: 9rpx;
              background-color: #00D4D1;
              border-radius: 5rpx;
            }
          }

          .category-text {
            font-size: 33rpx;
            color: #777777;
            font-weight: 500;
            line-height: 46rpx;

            &.active {
              color: #222222;
            }
          }
        }
      }
    }
  }

  // 需求列表
  .demand-list {
    padding: 0 30rpx 30rpx;

    .empty-tip {
      text-align: center;
      padding: 100rpx 0;
      color: #999;
      font-size: 28rpx;
    }
  }
}
</style>
