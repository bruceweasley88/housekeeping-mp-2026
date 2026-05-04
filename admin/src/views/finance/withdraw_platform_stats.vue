<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <div class="flex items-center gap-4">
                <span class="text-gray-500">统计周期</span>
                <el-radio-group v-model="period" @change="fetchData">
                    <el-radio-button value="week">本周</el-radio-button>
                    <el-radio-button value="month">本月</el-radio-button>
                    <el-radio-button value="year">本年</el-radio-button>
                </el-radio-group>
            </div>
        </el-card>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <el-card class="!border-none" shadow="never">
                <div class="text-center">
                    <div class="text-gray-500 text-sm">已提取</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ stats.settled_count }} 笔</div>
                    <div class="text-lg text-blue-600">¥{{ formatMoney(stats.settled_amount) }}</div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="text-center">
                    <div class="text-gray-500 text-sm">已拒绝</div>
                    <div class="text-2xl font-bold text-red-500 mt-1">{{ stats.rejected_count }} 笔</div>
                    <div class="text-lg text-red-500">¥{{ formatMoney(stats.rejected_amount) }}</div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="text-center">
                    <div class="text-gray-500 text-sm">待审核</div>
                    <div class="text-2xl font-bold text-orange-500 mt-1">{{ stats.pending_count }} 笔</div>
                    <div class="text-lg text-orange-500">¥{{ formatMoney(stats.pending_amount) }}</div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="text-center">
                    <div class="text-gray-500 text-sm">佣金收入</div>
                    <div class="text-lg text-gray-400 mt-1">基于当前费率计算</div>
                    <div class="text-2xl font-bold text-green-600">¥{{ formatMoney(stats.commission_amount) }}</div>
                </div>
            </el-card>
        </div>
    </div>
</template>

<script lang="ts" setup name="withdrawPlatformStats">
import { getPlatformStats } from '@/api/finance'

const period = ref('month')

const stats = ref({
    settled_count: 0,
    settled_amount: 0,
    rejected_count: 0,
    rejected_amount: 0,
    pending_count: 0,
    pending_amount: 0,
    commission_amount: 0,
})

const formatMoney = (val: number) => {
    return Number(val).toLocaleString('zh-CN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const fetchData = async () => {
    try {
        const res: any = await getPlatformStats({ period: period.value })
        stats.value = res
    } catch (err) {
        console.error('获取平台统计失败', err)
    }
}

fetchData()
</script>
