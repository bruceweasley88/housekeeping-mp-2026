<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item class="w-[280px]" label="用户信息">
                    <el-input
                        v-model="queryParams.keyword"
                        placeholder="账号/昵称/手机号码"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="注册时间">
                    <daterange-picker
                        v-model:startTime="queryParams.create_time_start"
                        v-model:endTime="queryParams.create_time_end"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="注册来源">
                    <el-select v-model="queryParams.channel">
                        <el-option
                            v-for="(item, key) in ClientMap"
                            :key="key"
                            :label="item"
                            :value="key"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="getUserList"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="头像" min-width="100">
                    <template #default="{ row }">
                        <el-avatar :src="row.avatar" :size="50" />
                    </template>
                </el-table-column>
                <el-table-column label="昵称" prop="nickname" min-width="100" />
                <el-table-column label="账号" prop="account" min-width="120" />
                <el-table-column label="手机号码" prop="mobile" min-width="100" />
                <el-table-column label="注册来源" prop="channel" min-width="100" />
                <el-table-column label="注册时间" prop="create_time" min-width="120" />
                <el-table-column label="操作" width="160" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleIncome(row)">收益</el-button>
                        <el-button v-perms="['user.user/detail']" type="primary" link>
                            <router-link
                                :to="{
                                    path: getRoutePath('user.user/detail'),
                                    query: {
                                        id: row.id
                                    }
                                }"
                            >
                                详情
                            </router-link>
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <!-- 收益弹窗 -->
        <el-dialog v-model="incomeDialogVisible" :title="incomeUser.nickname + ' - 用户收益'" width="700px" :close-on-click-modal="false">
            <!-- 汇总 -->
            <div class="flex gap-6 mb-4 text-sm text-gray-600">
                <span>总收入 <b class="text-base">¥{{ billSummary.income }}</b></span>
                <span>总支出 <b class="text-base">¥{{ billSummary.expense }}</b></span>
                <span>可用余额 <b class="text-base">¥{{ billSummary.balance }}</b></span>
            </div>

            <!-- Tab 切换 -->
            <el-tabs v-model="billTab" @tab-change="onBillTabChange">
                <el-tab-pane label="收入详情" :name="1" />
                <el-tab-pane label="支出详情" :name="2" />
            </el-tabs>

            <!-- 账单列表 -->
            <el-table size="large" v-loading="billLoading" :data="billList">
                <el-table-column prop="title" label="描述" min-width="160" show-overflow-tooltip />
                <el-table-column prop="time" label="时间" min-width="160" />
                <el-table-column label="金额" min-width="120" align="right">
                    <template #default="{ row }">
                        {{ row.type === 1 ? '+' : '-' }}¥{{ row.amount }}
                    </template>
                </el-table-column>
                <el-table-column label="状态" min-width="100" align="center">
                    <template #default="{ row }">
                        {{ row.type === 1 ? (row.status === 2 ? '已入账' : '待入账') : billStatusText(row.status) }}
                    </template>
                </el-table-column>
                <el-table-column label="备注" min-width="140">
                    <template #default="{ row }">
                        <span v-if="row.status === 3 && row.remark" class="text-gray-500">{{ row.remark }}</span>
                        <span v-else class="text-gray-400">-</span>
                    </template>
                </el-table-column>
            </el-table>

            <!-- 分页 -->
            <div class="flex justify-end mt-4">
                <el-pagination
                    v-model:current-page="billPage"
                    :page-size="billPageSize"
                    :total="billTotal"
                    layout="total, prev, pager, next"
                    small
                    @current-change="fetchBillData"
                />
            </div>
        </el-dialog>
    </div>
</template>
<script lang="ts" setup name="consumerLists">
import { getUserList } from '@/api/consumer'
import { getUserBillLists } from '@/api/finance'
import { ClientMap } from '@/enums/appEnums'
import { usePaging } from '@/hooks/usePaging'
import { getRoutePath } from '@/router'

const queryParams = reactive({
    keyword: '',
    channel: '',
    create_time_start: '',
    create_time_end: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getUserList,
    params: queryParams
})

// 收益弹窗
const incomeDialogVisible = ref(false)
const incomeUser = ref<any>({})
const billTab = ref(1)
const billLoading = ref(false)
const billList = ref<any[]>([])
const billTotal = ref(0)
const billPage = ref(1)
const billPageSize = 10

const billSummary = reactive({
    income: '0.00',
    expense: '0.00',
    balance: '0.00',
})

const billStatusText = (status: number) => {
    if (status === 2) return '已提取'
    if (status === 3) return '已拒绝'
    return '审核中'
}

const fetchBillData = async () => {
    billLoading.value = true
    try {
        const res: any = await getUserBillLists({
            user_id: incomeUser.value.id,
            type: billTab.value,
            page_no: billPage.value,
            page_size: billPageSize,
        })
        billSummary.income = res.income || '0.00'
        billSummary.expense = res.expense || '0.00'
        billSummary.balance = res.balance || '0.00'
        billList.value = res.list || []
        billTotal.value = res.count || 0
    } catch (err) {
        console.error('获取用户账单失败', err)
    } finally {
        billLoading.value = false
    }
}

const handleIncome = (row: any) => {
    incomeUser.value = row
    billTab.value = 1
    billPage.value = 1
    incomeDialogVisible.value = true
    fetchBillData()
}

const onBillTabChange = () => {
    billPage.value = 1
    fetchBillData()
}

onActivated(() => {
    getLists()
})

getLists()
</script>
