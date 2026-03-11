<template>
    <u-tabbar
        v-if="showTabbar"
        v-model="current"
        v-bind="tabbarStyle"
        :list="tabbarList"
        @change="handleChange"
        :hide-tab-bar="false"
    ></u-tabbar>
</template>

<script lang="ts" setup>
import { navigateTo } from '@/utils/util'
import { computed, ref } from 'vue'

const current = ref()

// 静态 tabbar 配置
const tabbarList = [
    {
        iconPath: '/static/nav_icon/icon_homen.png',
        selectedIconPath: '/static/nav_icon/icon_homes.png',
        text: '首页',
        pagePath: '/pages/index/index'
    },
    {
        iconPath: '/static/nav_icon/icon_helpn.png',
        selectedIconPath: '/static/nav_icon/icon_helps.png',
        text: '互助',
        pagePath: '/pages/mutual-aid-list/mutual-aid-list'
    },
    {
        iconPath: '/static/nav_icon/icon_talkn.png',
        selectedIconPath: '/static/nav_icon/icon_talks.png',
        text: '信息',
        pagePath: '/pages/message/message'
    },
    {
        iconPath: '/static/nav_icon/icon_myn.png',
        selectedIconPath: '/static/nav_icon/icon_mys.png',
        text: '我的',
        pagePath: '/pages/user/user'
    }
]

// 静态颜色配置
const tabbarStyle = {
    activeColor: '#00B6B4',
    inactiveColor: '#616B6B'
}

const showTabbar = computed(() => {
    const currentPages = getCurrentPages()
    const currentPage = currentPages[currentPages.length - 1]
    const current = tabbarList.findIndex((item) => {
        return item.pagePath === '/' + currentPage.route
    })
    return current >= 0
})

const handleChange = (index: number) => {
    const selectTab = tabbarList[index]
    uni.switchTab({
        url: selectTab.pagePath
    })
}
</script>
