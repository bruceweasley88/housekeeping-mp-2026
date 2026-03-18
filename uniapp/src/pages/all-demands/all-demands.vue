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
    <scroll-view
      class="demand-list"
      scroll-y
      @scrolltolower="handleLoadMore"
    >
      <view v-if="demandList.length === 0 && !loading" class="empty-tip">
        <text>暂无需求</text>
      </view>
      <view v-if="loading && demandList.length === 0" class="loading-wrap">
        <u-loadmore status="loading" />
      </view>
      <demand-card
        v-for="item in demandList"
        :key="item.id"
        :is-urgent="item.is_urgent"
        :title="item.title"
        :location="item.address"
        :description="item.description"
        :price-type="item.price_type"
        :amount="item.amount"
        :hour-price="item.hour_price"
        :min-amount="item.min_amount"
        :max-amount="item.max_amount"
        :image="item.images?.[0] || ''"
        :avatar="item.user_info?.avatar || ''"
        :username="item.user_info?.nickname || ''"
        :create-time="item.create_time"
        action-text="查看详情"
        @card-click="handleTakeOrder(item)"
        @action="handleTakeOrder(item)"
      />
      <!-- 加载更多状态 -->
      <u-loadmore
        v-if="demandList.length > 0"
        :status="loading ? 'loading' : (hasMore ? 'loadmore' : 'nomore')"
        load-text="上拉加载更多"
        nomore-text="没有更多了"
        @loadmore="handleLoadMore"
      />
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { getDemandCategoryLists, getDemandLists } from '@/api/demand'
import { getUserAddress } from '@/api/community'

// 分类列表
const categoryList = ref<{ id: number; name: string; icon: string }[]>([])

// 当前选中的分类ID
const currentCategoryId = ref(0)

// 需求列表
const demandList = ref<any[]>([])

// 搜索关键词
const keyword = ref('')

// 用户小区ID
const communityId = ref<number | null>(null)

// 加载状态
const loading = ref(false)

// 分页相关
const pageNo = ref(1)
const pageSize = 10
const hasMore = ref(true)
const total = ref(0)

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

// 获取用户地址
const fetchUserAddress = async () => {
  try {
    const res = await getUserAddress()
    if (res && res.community_id) {
      communityId.value = res.community_id
    }
  } catch (e) {
    communityId.value = null
  }
}

// 加载需求列表
const fetchDemandList = async (isLoadMore = false) => {
  if (!isLoadMore) {
    loading.value = true
    pageNo.value = 1
  }
  try {
    const params: any = {
      page_no: pageNo.value,
      page_size: pageSize
    }
    // 传递小区ID
    if (communityId.value) {
      params.community_id = communityId.value
    }
    // 只有非0的分类ID才传递
    if (currentCategoryId.value !== 0) {
      params.category_id = currentCategoryId.value
    }
    // 搜索关键词
    if (keyword.value) {
      params.keyword = keyword.value
    }

    const res = await getDemandLists(params)
    const list = res?.list || []
    total.value = res?.count || 0

    if (isLoadMore) {
      demandList.value = [...demandList.value, ...list]
    } else {
      demandList.value = list
    }
    // 判断是否还有更多
    hasMore.value = demandList.value.length < total.value
  } catch (e) {
    demandList.value = []
    hasMore.value = false
  } finally {
    loading.value = false
  }
}

// 加载更多
const handleLoadMore = () => {
  if (loading.value || !hasMore.value) return
  pageNo.value++
  fetchDemandList(true)
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

// 页面加载
onLoad((options) => {
  // 获取传入的分类ID
  const categoryId = options?.category_id ? parseInt(options.category_id) : 0
  currentCategoryId.value = categoryId

  // 先加载分类
  fetchCategoryList()
  // 获取用户地址后再加载需求
  fetchUserAddress().then(() => {
    fetchDemandList()
  })
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
    height: calc(100vh - 220rpx);

    .empty-tip {
      text-align: center;
      padding: 100rpx 0;
      color: #999;
      font-size: 28rpx;
    }

    .loading-wrap {
      padding: 60rpx 0;
    }

    // 给卡片添加左右边距
    :deep(.demand-card) {
      margin-left: 30rpx;
      margin-right: 30rpx;
    }
  }
}
</style>
