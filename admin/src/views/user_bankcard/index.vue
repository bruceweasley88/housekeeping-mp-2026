<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item class="w-[280px]" label="用户信息">
                    <el-input
                        v-model="queryParams.keyword"
                        placeholder="昵称/手机号"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="状态">
                    <el-select v-model="queryParams.status" clearable placeholder="请选择状态">
                        <el-option label="待审核" :value="0" />
                        <el-option label="已通过" :value="1" />
                        <el-option label="已拒绝" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item label="提交时间">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" min-width="60" />
                <el-table-column label="用户昵称" prop="nickname" min-width="100" show-overflow-tooltip />
                <el-table-column label="手机号" prop="mobile" min-width="120" />
                <el-table-column label="真实姓名" prop="real_name" min-width="100" />
                <el-table-column label="银行卡图片" min-width="100">
                    <template #default="{ row }">
                        <image-contain
                            v-if="row.bankcard_image"
                            :src="row.bankcard_image"
                            :width="60"
                            :height="40"
                            :preview-src-list="[row.bankcard_image]"
                            preview-teleported
                            fit="cover"
                        />
                        <span v-else class="text-[#999]">未上传</span>
                    </template>
                </el-table-column>
                <el-table-column label="状态" min-width="90">
                    <template #default="{ row }">
                        <el-tag v-if="row.status === 0" type="warning">待审核</el-tag>
                        <el-tag v-else-if="row.status === 1" type="success">已通过</el-tag>
                        <el-tag v-else-if="row.status === 2" type="danger">已拒绝</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="提交时间" prop="create_time" min-width="180" />
                <el-table-column label="操作" width="160" fixed="right">
                    <template #default="{ row }">
                        <template v-if="row.status === 0">
                            <el-button type="success" link @click="handleApprove(row.id)">
                                通过
                            </el-button>
                            <el-button type="danger" link @click="handleReject(row)">
                                拒绝
                            </el-button>
                        </template>
                        <span v-else class="text-[#999]">已处理</span>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <!-- 拒绝原因弹窗 -->
        <el-dialog v-model="rejectVisible" title="拒绝审核" width="500px" @close="handleRejectClose">
            <el-form ref="rejectFormRef" :model="rejectForm" :rules="rejectRules" label-width="80px">
                <el-form-item label="拒绝原因" prop="reject_reason">
                    <el-input
                        v-model="rejectForm.reject_reason"
                        type="textarea"
                        :rows="4"
                        placeholder="请输入拒绝原因"
                        maxlength="200"
                        show-word-limit
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="rejectVisible = false">取消</el-button>
                <el-button type="danger" :loading="rejectLoading" @click="handleRejectConfirm">确认拒绝</el-button>
            </template>
        </el-dialog>
    </div>
</template>
<script lang="ts" setup name="userBankcardLists">
import { getUserBankcardLists, userBankcardAudit } from '@/api/user_bankcard'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    keyword: '',
    status: '' as string | number,
    start_time: '',
    end_time: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getUserBankcardLists,
    params: queryParams
})

// 通过
const handleApprove = async (id: number) => {
    await feedback.confirm('确定通过该用户的银行卡认证审核？')
    await userBankcardAudit({ id, status: 1 })
    feedback.msgSuccess('审核通过')
    getLists()
}

// 拒绝
const rejectVisible = ref(false)
const rejectLoading = ref(false)
const rejectFormRef = ref()
const rejectForm = reactive({
    id: 0,
    reject_reason: ''
})
const rejectRules = {
    reject_reason: [{ required: true, message: '请输入拒绝原因', trigger: 'blur' }]
}

const handleReject = (row: any) => {
    rejectForm.id = row.id
    rejectForm.reject_reason = ''
    rejectVisible.value = true
}

const handleRejectClose = () => {
    rejectFormRef.value?.resetFields()
}

const handleRejectConfirm = async () => {
    await rejectFormRef.value?.validate()
    rejectLoading.value = true
    try {
        await userBankcardAudit({ id: rejectForm.id, status: 2, reject_reason: rejectForm.reject_reason })
        feedback.msgSuccess('已拒绝')
        rejectVisible.value = false
        getLists()
    } finally {
        rejectLoading.value = false
    }
}

onActivated(() => {
    getLists()
})

getLists()
</script>
