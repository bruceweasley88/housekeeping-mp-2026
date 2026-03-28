<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item class="w-[280px]" label="海报名称">
                    <el-input v-model="queryParams.name" placeholder="请输入名称" clearable @keyup.enter="resetPage" />
                </el-form-item>
                <el-form-item class="w-[280px]" label="状态">
                    <el-select v-model="queryParams.is_show" clearable placeholder="请选择状态">
                        <el-option label="显示" :value="1" />
                        <el-option label="隐藏" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <div>
                <el-button type="primary" @click="handleAdd">
                    <template #icon>
                        <icon name="el-icon-Plus" />
                    </template>
                    新增
                </el-button>
                <el-button :disabled="!selectData.length" type="danger" @click="handleDelete(selectData)">
                    <template #icon>
                        <icon name="el-icon-Delete" />
                    </template>
                    删除
                </el-button>
            </div>
            <div class="mt-4">
                <el-table size="large" v-loading="pager.loading" :data="pager.lists" @selection-change="handleSelectionChange">
                    <el-table-column type="selection" width="55" />
                    <el-table-column label="ID" prop="id" min-width="60" />
                    <el-table-column label="海报图片" min-width="100">
                        <template #default="{ row }">
                            <image-contain
                                v-if="row.image"
                                :src="row.image"
                                :width="60"
                                :height="40"
                                :preview-src-list="[row.image]"
                                preview-teleported
                                fit="cover"
                            />
                            <span v-else class="text-[#999]">未上传</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="海报名称" prop="name" min-width="120" show-overflow-tooltip />
                    <el-table-column label="排序" prop="sort" min-width="80" />
                    <el-table-column label="状态" min-width="80">
                        <template #default="{ row }">
                            <el-tag v-if="row.is_show === 1" type="success">显示</el-tag>
                            <el-tag v-else type="info">隐藏</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column label="创建时间" prop="create_time" min-width="180" />
                    <el-table-column label="操作" width="160" fixed="right">
                        <template #default="{ row }">
                            <el-button type="primary" link @click="handleEdit(row)">编辑</el-button>
                            <el-button type="danger" link @click="handleDelete(row.id)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <edit-popup v-if="showEdit" ref="editRef" @success="getLists" @close="showEdit = false" />
    </div>
</template>
<script lang="ts" setup name="posterLists">
import { getPosterLists, posterDelete } from '@/api/poster'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'

const editRef = shallowRef<InstanceType<typeof EditPopup>>()
const showEdit = ref(false)
const queryParams = reactive({
    name: '',
    is_show: '' as string | number
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getPosterLists,
    params: queryParams
})

const selectData = ref<any[]>([])

const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map(({ id }) => id)
}

const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}

const handleEdit = async (data: any) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit')
    editRef.value?.setFormData(data)
}

const handleDelete = async (id: any[] | number) => {
    await feedback.confirm('确定要删除？')
    await posterDelete({ id })
    getLists()
}

getLists()
</script>
