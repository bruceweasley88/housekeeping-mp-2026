<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form class="mb-[-16px]" :model="queryParams" :inline="true">
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
                        <el-option label="启用" :value="1" />
                        <el-option label="禁用" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleAdd">
                        添加客服
                    </el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" min-width="60" />
                <el-table-column label="头像" min-width="80">
                    <template #default="{ row }">
                        <el-avatar :src="row.avatar" :size="40" />
                    </template>
                </el-table-column>
                <el-table-column label="用户昵称" prop="nickname" min-width="120" show-overflow-tooltip />
                <el-table-column label="手机号" prop="mobile" min-width="120" />
                <el-table-column label="状态" min-width="80">
                    <template #default="{ row }">
                        <el-switch
                            :model-value="row.status === 1"
                            @change="(val: any) => handleStatus(row, val)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="添加时间" prop="create_time" min-width="180" />
                <el-table-column label="操作" width="100" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            type="danger"
                            link
                            @click="handleRemove(row.id)"
                        >
                            移除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <!-- 添加客服弹窗 -->
        <el-dialog v-model="addVisible" title="添加客服" width="800px" @close="handleAddClose">
            <el-form :inline="true" :model="userQueryParams" class="mb-[-16px]">
                <el-form-item class="w-[240px]">
                    <el-input
                        v-model="userQueryParams.keyword"
                        placeholder="账号/昵称/手机号码"
                        clearable
                        @keyup.enter="resetUserPage"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetUserPage">搜索</el-button>
                </el-form-item>
            </el-form>
            <el-table
                size="large"
                v-loading="userPager.loading"
                :data="userPager.lists"
                highlight-current-row
                @current-change="handleUserSelect"
                max-height="400"
            >
                <el-table-column label="头像" min-width="70">
                    <template #default="{ row }">
                        <el-avatar :src="row.avatar" :size="36" />
                    </template>
                </el-table-column>
                <el-table-column label="昵称" prop="nickname" min-width="120" />
                <el-table-column label="账号" prop="account" min-width="120" />
                <el-table-column label="手机号" prop="mobile" min-width="120" />
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="userPager" @change="getUserLists" />
            </div>
            <template #footer>
                <el-button @click="addVisible = false">取消</el-button>
                <el-button type="primary" :disabled="!selectedUser" @click="handleAddConfirm">
                    确认添加
                </el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script lang="ts" setup name="kefuLists">
import { getKefuLists, kefuAdd, kefuRemove, kefuStatus, getUserList } from '@/api/kefu'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

// 客服列表
const queryParams = reactive({
    keyword: '',
    status: '' as string | number,
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getKefuLists,
    params: queryParams,
})

// 移除客服
const handleRemove = async (id: number) => {
    await feedback.confirm('确定移除该客服？')
    await kefuRemove({ id })
    feedback.msgSuccess('移除成功')
    getLists()
}

// 切换状态
const handleStatus = async (row: any, val: any) => {
    await kefuStatus({ id: row.id, status: val ? 1 : 0 })
    feedback.msgSuccess('操作成功')
    getLists()
}

// 添加客服弹窗
const addVisible = ref(false)
const selectedUser = ref<any>(null)

const userQueryParams = reactive({
    keyword: '',
})

const { pager: userPager, getLists: getUserLists, resetPage: resetUserPage } = usePaging({
    fetchFun: getUserList,
    params: userQueryParams,
})

const handleAdd = () => {
    selectedUser.value = null
    addVisible.value = true
    nextTick(() => {
        resetUserPage()
    })
}

const handleAddClose = () => {
    selectedUser.value = null
}

const handleUserSelect = (row: any) => {
    selectedUser.value = row
}

const handleAddConfirm = async () => {
    if (!selectedUser.value) return
    await kefuAdd({ user_id: selectedUser.value.id })
    feedback.msgSuccess('添加成功')
    addVisible.value = false
    getLists()
}

onActivated(() => {
    getLists()
})

getLists()
</script>
