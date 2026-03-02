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
          placeholder="请输入任务关键词"
          placeholder-style="color: #999999;"
        />
        <view class="search-btn">
          <text class="search-btn-text">搜索</text>
        </view>
      </view>
    </view>

    <!-- 分类标签栏 -->
    <view class="category-section">
      <scroll-view class="category-scroll" scroll-x="true" show-scrollbar="false">
        <view class="category-list">
          <view class="category-item active">
            <text class="category-text active">最新需求</text>
            <view class="category-indicator"></view>
          </view>
          <view class="category-item">
            <text class="category-text">轻需求</text>
          </view>
          <view class="category-item">
            <text class="category-text">接送需求</text>
          </view>
          <view class="category-item">
            <text class="category-text">维修需求</text>
          </view>
          <view class="category-item">
            <text class="category-text">陪伴需求</text>
          </view>
        </view>
      </scroll-view>
    </view>

    <!-- 需求列表 -->
    <view class="demand-list">
      <demand-card
        tag="紧急"
        title="小三数学家教2小时"
        location="保利天悦A10"
        description="需要找小学三年级数学家教，周六日两天晚上19:00-21:00上课，合适的可长期…"
        price="300"
        priceUnit="元/小时"
        image="./assets/img/img_demand_sample.png"
        avatar="./assets/img/icon_avatar_default.png"
        username="小伟妈妈"
        publishTime="发布于2026.01.02 12:00"
        actionText="承接需求"
        @action="handleTakeOrder"
        @location="handleLocationClick"
      />
      <demand-card
        title="清洁打扫房间"
        location="保利天悦B15"
        description="需要找女性，爱干净有经验，周六日两天白天09:00-12:00打扫，合适的可长期…"
        price="180"
        priceUnit="元/次"
        image=""
        avatar="./assets/img/icon_avatar_default.png"
        username="小伟妈妈"
        publishTime="发布于2026.01.02 12:00"
        actionText="承接需求"
        @action="handleTakeOrder"
        @location="handleLocationClick"
      />
      <demand-card
        title="清洁打扫房间"
        location="保利天悦B15"
        description="需要找女性，爱干净有经验，周六日两天白天09:00-12:00打扫，合适的可长期…"
        price="180"
        priceUnit="元/次"
        image=""
        avatar="./assets/img/icon_avatar_default.png"
        username="小伟妈妈"
        publishTime="发布于2026.01.02 12:00"
        actionText="承接需求"
        @action="handleTakeOrder"
        @location="handleLocationClick"
      />
    </view>
  </view>
</template>

<script setup lang="ts">
// 承接需求按钮点击
const handleTakeOrder = () => {
  uni.showToast({
    title: '承接需求',
    icon: 'none'
  })
}

// 地址点击
const handleLocationClick = () => {
  uni.showToast({
    title: '查看位置',
    icon: 'none'
  })
}
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
  }
}
</style>
