<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item class="w-[280px]" label="小区名称">
                    <el-input
                        v-model="queryParams.name"
                        placeholder="请输入小区名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="省份">
                    <el-input
                        v-model="queryParams.province"
                        placeholder="请输入省份"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="城市">
                    <el-input
                        v-model="queryParams.city"
                        placeholder="请输入城市"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="区县">
                    <el-input
                        v-model="queryParams.district"
                        placeholder="请输入区县"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="状态">
                    <el-select v-model="queryParams.status" clearable placeholder="请选择状态">
                        <el-option label="待审核" :value="0" />
                        <el-option label="已启用" :value="1" />
                        <el-option label="已拒绝" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item label="创建时间">
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
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="小区名称" prop="name" min-width="120" show-overflow-tooltip />
                <el-table-column label="省份" prop="province" min-width="80" />
                <el-table-column label="城市" prop="city" min-width="80" />
                <el-table-column label="区县" prop="district" min-width="80" />
                <el-table-column label="详细地址" prop="address" min-width="160" show-overflow-tooltip />
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.status === 0" type="warning">待审核</el-tag>
                        <el-tag v-else-if="row.status === 1" type="success">已启用</el-tag>
                        <el-tag v-else-if="row.status === 2" type="danger">已拒绝</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="创建时间" prop="create_time" min-width="180" />
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <template v-if="row.status === 0">
                            <el-button
                                type="success"
                                link
                                @click="handleAudit(row.id, 1)"
                            >
                                通过
                            </el-button>
                            <el-button
                                type="danger"
                                link
                                @click="handleAudit(row.id, 2)"
                            >
                                拒绝
                            </el-button>
                        </template>
                        <template v-else>
                            <span class="text-[#999]">已处理</span>
                        </template>
                        <el-button
                            type="danger"
                            link
                            @click="handleDelete(row.id)"
                        >
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>
<script lang="ts" setup name="communityLists">
import { getCommunityLists, communityAudit, communityDelete } from '@/api/community'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    name: '',
    province: '',
    city: '',
    district: '',
    status: '' as string | number,
    start_time: '',
    end_time: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getCommunityLists,
    params: queryParams
})

const handleAudit = async (id: number, status: number) => {
    const actionText = status === 1 ? '通过' : '拒绝'
    await feedback.confirm(`确定${actionText}该小区的审核？`)
    await communityAudit({ id, status })
    feedback.msgSuccess('审核成功')
    getLists()
}

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除该小区？')
    await communityDelete({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

onActivated(() => {
    getLists()
})

getLists()
</script>
