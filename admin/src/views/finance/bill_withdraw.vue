<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px] mt-[16px]" :model="queryParams" :inline="true">
                <el-form-item class="w-[280px]" label="用户信息">
                    <el-input
                        v-model="queryParams.user_info"
                        placeholder="昵称/手机号"
                        clearable
                        @keyup.enter="onSearch"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="状态">
                    <el-select v-model="queryParams.status" clearable placeholder="全部">
                        <el-option label="审核中" :value="1" />
                        <el-option label="已提取" :value="2" />
                        <el-option label="已拒绝" :value="3" />
                    </el-select>
                </el-form-item>
                <el-form-item label="申请时间">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="onSearch">查询</el-button>
                    <el-button @click="onReset">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <!-- 统计卡片 -->
        <div class="flex gap-4 mt-4">
            <el-card class="!border-none flex-1" shadow="never">
                <div class="text-center">
                    <div class="text-gray-500 text-sm">已提取</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ summaryData.total.count }} 笔</div>
                    <div class="text-lg text-blue-600">¥{{ formatMoney(summaryData.total.amount) }}</div>
                </div>
            </el-card>
            <el-card class="!border-none flex-1" shadow="never">
                <div class="text-center">
                    <div class="text-gray-500 text-sm">已拒绝</div>
                    <div class="text-2xl font-bold text-red-500 mt-1">{{ summaryData.rejected.count }} 笔</div>
                    <div class="text-lg text-red-500">¥{{ formatMoney(summaryData.rejected.amount) }}</div>
                </div>
            </el-card>
        </div>

        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="用户信息" min-width="140">
                    <template #default="{ row }">
                        <div>{{ row.nickname }}</div>
                        <div class="text-xs text-gray-400">{{ row.mobile }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="提现金额" min-width="120">
                    <template #default="{ row }">
                        ¥{{ row.amount }}
                    </template>
                </el-table-column>
                <el-table-column label="手续费率" min-width="120">
                    <template #default="{ row }">
                        3%
                    </template>
                </el-table-column>
                <el-table-column label="到手金额" min-width="120">
                    <template #default="{ row }">
                        ¥{{ (row.amount * 0.97).toFixed(2) }}
                    </template>
                </el-table-column>
                <el-table-column label="状态" min-width="120">
                    <template #default="{ row }">
                        <el-tag v-if="row.status === 1" type="warning">审核中</el-tag>
                        <el-tag v-else-if="row.status === 2" type="success">已提取</el-tag>
                        <el-tag v-else-if="row.status === 3" type="danger">已拒绝</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="申请时间" prop="create_time_desc" min-width="160" />
                <el-table-column label="银行卡信息" min-width="160">
                    <template #default="{ row }">
                        <template v-if="row.bank_real_name">
                            <div>{{ row.bank_real_name }}</div>
                            <el-image
                                v-if="row.bankcard_image"
                                :src="row.bankcard_image"
                                :preview-src-list="[row.bankcard_image]"
                                fit="cover"
                                style="width: 60px; height: 40px; margin-top: 4px"
                                preview-teleported
                            />
                        </template>
                        <span v-else class="text-gray-400">未上传</span>
                    </template>
                </el-table-column>
                <el-table-column label="拒绝原因" min-width="140">
                    <template #default="{ row }">
                        <span v-if="row.status === 3" class="text-red-500">{{ row.remark }}</span>
                        <span v-else>-</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="160" fixed="right">
                    <template #default="{ row }">
                        <template v-if="row.status === 1">
                            <el-button
                                type="primary"
                                link
                                @click="handleApprove(row.id)"
                            >
                                通过
                            </el-button>
                            <el-button
                                type="danger"
                                link
                                @click="handleReject(row.id)"
                            >
                                拒绝
                            </el-button>
                        </template>
                        <span v-else class="text-gray-400">-</span>
                    </template>
                </el-table-column>
            </el-table>

            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <!-- 拒绝原因弹窗 -->
        <el-dialog v-model="showRejectDialog" title="拒绝提现" width="440px" :close-on-click-modal="false">
            <el-form ref="rejectFormRef" :model="rejectForm" :rules="rejectRules">
                <el-form-item label="拒绝原因" prop="remark">
                    <el-input
                        v-model="rejectForm.remark"
                        type="textarea"
                        :rows="4"
                        placeholder="请输入拒绝原因"
                        maxlength="200"
                        show-word-limit
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="showRejectDialog = false">取消</el-button>
                <el-button type="primary" @click="confirmReject" :loading="rejectLoading">确认拒绝</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script lang="ts" setup name="billWithdraw">
import { getWithdrawLists, approveWithdraw, rejectWithdraw, getWithdrawSummary } from '@/api/finance'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    user_info: '',
    status: '' as string | number,
    start_time: '',
    end_time: '',
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getWithdrawLists,
    params: queryParams,
})

// 统计数据
const summaryData = ref({
    total: { count: 0, amount: 0 },
    rejected: { count: 0, amount: 0 },
})

const formatMoney = (val: number) => {
    return Number(val).toLocaleString('zh-CN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const fetchSummary = async () => {
    try {
        const res: any = await getWithdrawSummary({
            user_info: queryParams.user_info,
            start_time: queryParams.start_time,
            end_time: queryParams.end_time,
        })
        summaryData.value = res
    } catch (err) {
        console.error('获取提现统计失败', err)
    }
}

const refreshData = () => {
    getLists()
    fetchSummary()
}

const onSearch = () => {
    resetPage()
    fetchSummary()
}

const onReset = () => {
    resetParams()
    fetchSummary()
}

// 审核通过
const handleApprove = async (id: number) => {
    await feedback.confirm('确认通过该提现申请？')
    await approveWithdraw({ id })
    feedback.msgSuccess('审核通过')
    refreshData()
}

// 拒绝弹窗
const showRejectDialog = ref(false)
const rejectLoading = ref(false)
const rejectFormRef = ref()
const rejectForm = reactive({
    id: 0,
    remark: '',
})
const rejectRules = {
    remark: [{ required: true, message: '请输入拒绝原因', trigger: 'blur' }],
}

const handleReject = (id: number) => {
    rejectForm.id = id
    rejectForm.remark = ''
    showRejectDialog.value = true
}

const confirmReject = async () => {
    await rejectFormRef.value?.validate()
    rejectLoading.value = true
    try {
        await rejectWithdraw({ id: rejectForm.id, remark: rejectForm.remark })
        feedback.msgSuccess('已拒绝')
        showRejectDialog.value = false
        refreshData()
    } finally {
        rejectLoading.value = false
    }
}

getLists()
fetchSummary()
</script>
