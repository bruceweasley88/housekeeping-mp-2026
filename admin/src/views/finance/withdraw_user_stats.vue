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

        <el-card class="!border-none mt-4" shadow="never">
            <el-table
                :data="userList"
                v-loading="loading"
                row-key="user_id"
                @expand-change="handleExpand"
            >
                <el-table-column type="expand">
                    <template #default="{ row }">
                        <div class="px-12 py-2">
                            <el-table :data="row.details" size="small" v-loading="row._detailLoading">
                                <el-table-column label="账单编号" prop="bill_no" min-width="160" />
                                <el-table-column label="提现金额" min-width="120" align="right">
                                    <template #default="{ row: detail }">
                                        ¥{{ detail.amount }}
                                    </template>
                                </el-table-column>
                                <el-table-column label="状态" min-width="100" align="center">
                                    <template #default="{ row: detail }">
                                        <el-tag v-if="detail.status === 1" type="warning" size="small">审核中</el-tag>
                                        <el-tag v-else-if="detail.status === 2" type="success" size="small">已提取</el-tag>
                                        <el-tag v-else-if="detail.status === 3" type="danger" size="small">已拒绝</el-tag>
                                    </template>
                                </el-table-column>
                                <el-table-column label="备注" prop="remark" min-width="140" show-overflow-tooltip>
                                    <template #default="{ row: detail }">
                                        {{ detail.remark || '-' }}
                                    </template>
                                </el-table-column>
                                <el-table-column label="申请时间" prop="create_time_desc" min-width="160" />
                            </el-table>
                            <div v-if="!row._detailLoading && (!row.details || row.details.length === 0)" class="text-center text-gray-400 py-4">
                                暂无提现记录
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="用户信息" min-width="140">
                    <template #default="{ row }">
                        <div>{{ row.nickname }}</div>
                        <div class="text-xs text-gray-400">{{ row.mobile }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="已提取" min-width="140" align="right">
                    <template #default="{ row }">
                        <span class="text-blue-600">{{ row.settled_count }} 笔</span>
                        <span class="ml-2">¥{{ formatMoney(row.settled_amount) }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="已拒绝" min-width="140" align="right">
                    <template #default="{ row }">
                        <span class="text-red-500">{{ row.rejected_count }} 笔</span>
                        <span class="ml-2">¥{{ formatMoney(row.rejected_amount) }}</span>
                    </template>
                </el-table-column>
            </el-table>

            <div class="flex justify-end mt-4">
                <el-pagination
                    v-model:current-page="pageNo"
                    v-model:page-size="pageSize"
                    :total="total"
                    layout="total, sizes, prev, pager, next"
                    :page-sizes="[15, 30, 50]"
                    @current-change="fetchData"
                    @size-change="fetchData"
                />
            </div>
        </el-card>
    </div>
</template>

<script lang="ts" setup name="withdrawUserStats">
import { getUserStats, getUserStatsDetail } from '@/api/finance'

const period = ref('month')
const loading = ref(false)
const userList = ref<any[]>([])
const total = ref(0)
const pageNo = ref(1)
const pageSize = ref(15)

const expandedUsers = ref<Set<number>>(new Set())

const formatMoney = (val: number) => {
    return Number(val).toLocaleString('zh-CN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const fetchData = async () => {
    loading.value = true
    try {
        const res: any = await getUserStats({
            period: period.value,
            page_no: pageNo.value,
            page_size: pageSize.value,
        })
        userList.value = (res.list || []).map((item: any) => ({
            ...item,
            details: [],
            _detailLoading: false,
        }))
        total.value = res.count || 0
        expandedUsers.value.clear()
    } catch (err) {
        console.error('获取用户统计失败', err)
    } finally {
        loading.value = false
    }
}

const handleExpand = async (row: any, expandedRows: any[]) => {
    const isExpanded = expandedRows.some((r: any) => r.user_id === row.user_id)
    if (!isExpanded) return

    if (row.details && row.details.length > 0) return

    row._detailLoading = true
    try {
        const res: any = await getUserStatsDetail({
            user_id: row.user_id,
            period: period.value,
        })
        row.details = res || []
    } catch (err) {
        console.error('获取用户提现明细失败', err)
        row.details = []
    } finally {
        row._detailLoading = false
    }
}

fetchData()
</script>
