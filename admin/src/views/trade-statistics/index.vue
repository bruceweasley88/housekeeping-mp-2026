<template>
    <div class="trade-statistics">
        <!-- 全局筛选器 -->
        <el-card class="!border-none" shadow="never">
            <div class="flex items-center gap-4 flex-wrap">
                <el-radio-group v-model="filterState.date_type" @change="onQuickDate">
                    <el-radio-button value="week">本周</el-radio-button>
                    <el-radio-button value="month">本月</el-radio-button>
                    <el-radio-button value="year">本年</el-radio-button>
                </el-radio-group>
                <daterange-picker
                    v-model:startTime="filterState.start_date"
                    v-model:endTime="filterState.end_date"
                    @change="onDateChange"
                />
            </div>
        </el-card>

        <!-- 成交趋势 + 发布中统计 -->
        <div class="lg:flex gap-4 mt-4">
            <!-- 成交趋势折线图 -->
            <el-card class="!border-none flex-1" shadow="never">
                <template #header>
                    <div class="flex items-center justify-between">
                        <span>成交趋势</span>
                        <div class="text-sm text-gray-500">
                            总成交 <span class="text-lg font-bold text-blue-600">{{ trendData.summary?.total_count || 0 }}</span> 单，
                            总金额 <span class="text-lg font-bold text-blue-600">¥{{ formatMoney(trendData.summary?.total_amount || 0) }}</span>
                        </div>
                    </div>
                </template>
                <v-charts
                    style="height: 350px"
                    :option="trendOption"
                    :autoresize="true"
                />
            </el-card>

            <!-- 发布中任务统计 -->
            <el-card class="!border-none lg:w-[380px]" shadow="never">
                <template #header>
                    <div class="flex items-center justify-between">
                        <span>发布中任务</span>
                        <span class="text-sm">
                            总数 <span class="text-lg font-bold text-orange-500">{{ publishingData.total_count || 0 }}</span> 单
                        </span>
                    </div>
                </template>
                <v-charts
                    style="height: 300px"
                    :option="publishingPieOption"
                    :autoresize="true"
                />
            </el-card>
        </div>

        <!-- 各小区成交统计 -->
        <el-card class="!border-none mt-4" shadow="never">
            <template #header>
                <span>各小区成交统计</span>
            </template>
            <div class="lg:flex gap-4">
                <!-- 柱状图 -->
                <div class="lg:w-3/5">
                    <v-charts
                        style="height: 400px"
                        :option="communityBarOption"
                        :autoresize="true"
                    />
                </div>
                <!-- 分页表格 -->
                <div class="lg:w-2/5">
                    <el-table :data="communityTableData" size="large" max-height="400">
                        <el-table-column label="排名" type="index" width="60" :index="indexMethod" />
                        <el-table-column prop="community_name" label="小区" min-width="120" show-overflow-tooltip />
                        <el-table-column prop="count" label="成交数" width="80" align="center" />
                        <el-table-column prop="amount" label="成交金额" width="100" align="right">
                            <template #default="{ row }">
                                ¥{{ formatMoney(row.amount) }}
                            </template>
                        </el-table-column>
                    </el-table>
                    <div class="flex justify-end mt-3">
                        <el-pagination
                            v-model:current-page="communityPage"
                            :page-size="communityPageSize"
                            :total="communityTotal"
                            layout="total, prev, pager, next"
                            small
                            @current-change="fetchCommunityData"
                        />
                    </div>
                </div>
            </div>
        </el-card>

        <!-- 按任务类型统计 -->
        <el-card class="!border-none mt-4" shadow="never">
            <template #header>
                <span>按任务类型统计</span>
            </template>
            <el-table :data="categoryData" size="large">
                <el-table-column label="序号" type="index" width="60" />
                <el-table-column prop="category_name" label="任务类型" min-width="150">
                    <template #default="{ row }">
                        {{ row.category_name || '未分类' }}
                    </template>
                </el-table-column>
                <el-table-column prop="count" label="成交任务数" width="120" align="center" />
                <el-table-column prop="amount" label="成交金额" width="140" align="right">
                    <template #default="{ row }">
                        ¥{{ formatMoney(row.amount) }}
                    </template>
                </el-table-column>
                <el-table-column prop="avg_amount" label="平均单价" width="140" align="right">
                    <template #default="{ row }">
                        ¥{{ formatMoney(row.avg_amount) }}
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script lang="ts" setup name="tradeStatistics">
import vCharts from 'vue-echarts'
import { getTradeSummary, getCommunityStats } from '@/api/trade-statistics'

// 筛选器状态
const filterState = reactive({
    date_type: 'week' as string,
    start_date: '',
    end_date: ''
})

// 各区域数据
const trendData = ref<any>({})
const categoryData = ref<any[]>([])
const publishingData = ref<any>({})
const communityChartData = ref<any[]>([])
const communityTableData = ref<any[]>([])
const communityTotal = ref(0)
const communityPage = ref(1)
const communityPageSize = 15

// 格式化金额
const formatMoney = (val: number) => {
    return Number(val).toLocaleString('zh-CN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

// 表格排名序号（考虑分页）
const indexMethod = (index: number) => {
    return (communityPage.value - 1) * communityPageSize + index + 1
}

// 获取汇总数据
const fetchSummaryData = async () => {
    const params = {
        date_type: filterState.date_type,
        start_date: filterState.start_date,
        end_date: filterState.end_date
    }
    try {
        const res: any = await getTradeSummary(params)
        trendData.value = res.trend || {}
        categoryData.value = res.category_detail || []
        publishingData.value = res.publishing || {}
    } catch (err) {
        console.error('获取交易统计汇总失败', err)
    }
}

// 获取小区数据
const fetchCommunityData = async () => {
    const params = {
        date_type: filterState.date_type,
        start_date: filterState.start_date,
        end_date: filterState.end_date,
        page: communityPage.value,
        page_size: communityPageSize
    }
    try {
        const res: any = await getCommunityStats(params)
        communityChartData.value = res.chart_list || []
        communityTableData.value = res.table_list || []
        communityTotal.value = res.count || 0
    } catch (err) {
        console.error('获取小区统计失败', err)
    }
}

// 并行加载所有数据
const fetchAllData = () => {
    communityPage.value = 1
    Promise.all([fetchSummaryData(), fetchCommunityData()])
}

// 快捷日期切换
const onQuickDate = () => {
    filterState.start_date = ''
    filterState.end_date = ''
    fetchAllData()
}

// 自定义日期变化
const onDateChange = () => {
    if (filterState.start_date && filterState.end_date) {
        filterState.date_type = 'custom'
        fetchAllData()
    }
}

// 成交趋势折线图配置
const trendOption = computed(() => ({
    tooltip: {
        trigger: 'axis',
        axisPointer: { type: 'cross' }
    },
    legend: {
        data: ['成交数', '成交金额']
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'category',
        data: trendData.value.dates || [],
        axisLabel: {
            rotate: trendData.value.dates?.length > 15 ? 45 : 0
        }
    },
    yAxis: [
        {
            type: 'value',
            name: '成交数',
            position: 'left'
        },
        {
            type: 'value',
            name: '金额(元)',
            position: 'right'
        }
    ],
    series: [
        {
            name: '成交数',
            type: 'line',
            smooth: true,
            data: trendData.value.counts || [],
            itemStyle: { color: '#409EFF' },
            areaStyle: {
                color: {
                    type: 'linear',
                    x: 0, y: 0, x2: 0, y2: 1,
                    colorStops: [
                        { offset: 0, color: 'rgba(64,158,255,0.3)' },
                        { offset: 1, color: 'rgba(64,158,255,0.05)' }
                    ]
                }
            }
        },
        {
            name: '成交金额',
            type: 'bar',
            smooth: true,
            yAxisIndex: 1,
            data: trendData.value.amounts || [],
            itemStyle: {
                color: {
                    type: 'linear',
                    x: 0, y: 0, x2: 0, y2: 1,
                    colorStops: [
                        { offset: 0, color: 'rgba(103,194,58,0.7)' },
                        { offset: 1, color: 'rgba(103,194,58,0.3)' }
                    ]
                },
                borderRadius: [4, 4, 0, 0]
            },
            barMaxWidth: 30
        }
    ]
}))

// 发布中任务饼图配置
const publishingPieOption = computed(() => ({
    tooltip: {
        trigger: 'item',
        formatter: '{b}: {c}单 ({d}%)'
    },
    legend: {
        bottom: 0,
        type: 'scroll'
    },
    series: [
        {
            type: 'pie',
            radius: ['40%', '70%'],
            center: ['50%', '45%'],
            avoidLabelOverlap: false,
            itemStyle: {
                borderRadius: 6,
                borderColor: '#fff',
                borderWidth: 2
            },
            label: {
                show: false
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: 14,
                    fontWeight: 'bold'
                }
            },
            data: (publishingData.value.by_category || []).map((item: any) => ({
                name: item.category_name || '未分类',
                value: item.count
            }))
        }
    ]
}))

// 小区柱状图配置
const communityBarOption = computed(() => ({
    tooltip: {
        trigger: 'axis',
        axisPointer: { type: 'shadow' },
        formatter: (params: any) => {
            const name = params[0].name
            const count = params[0].value
            const amount = params[1].value
            return `${name}<br/>成交数: ${count}单<br/>成交金额: ¥${formatMoney(amount)}`
        }
    },
    legend: {
        data: ['成交数', '成交金额']
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'category',
        data: communityChartData.value.map((item: any) => item.community_name || '未指定'),
        axisLabel: {
            rotate: 30,
            interval: 0
        }
    },
    yAxis: [
        { type: 'value', name: '成交数', position: 'left' },
        { type: 'value', name: '金额(元)', position: 'right' }
    ],
    series: [
        {
            name: '成交数',
            type: 'bar',
            data: communityChartData.value.map((item: any) => item.count),
            itemStyle: { color: '#409EFF', borderRadius: [4, 4, 0, 0] },
            barMaxWidth: 30
        },
        {
            name: '成交金额',
            type: 'bar',
            yAxisIndex: 1,
            data: communityChartData.value.map((item: any) => item.amount),
            itemStyle: { color: '#67C23A', borderRadius: [4, 4, 0, 0] },
            barMaxWidth: 30
        }
    ]
}))

onMounted(() => {
    fetchAllData()
})
</script>

<style lang="scss" scoped></style>
