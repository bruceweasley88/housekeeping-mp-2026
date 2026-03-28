<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="84px"
            >
                <el-form-item label="海报名称" prop="name">
                    <el-input v-model="formData.name" placeholder="请输入海报名称" clearable />
                </el-form-item>
                <el-form-item label="海报图片" prop="image">
                    <material-picker v-model="formData.image" />
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <el-input-number v-model="formData.sort" :min="0" :max="9999" />
                </el-form-item>
                <el-form-item label="状态" prop="is_show">
                    <el-switch
                        v-model="formData.is_show"
                        :active-value="1"
                        :inactive-value="0"
                        active-text="显示"
                        inactive-text="隐藏"
                    />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import { posterAdd, posterEdit } from '@/api/poster'
import Popup from '@/components/popup/index.vue'

const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '编辑海报' : '新增海报'
})

const formData = reactive({
    id: '',
    name: '',
    image: '',
    sort: 0,
    is_show: 1
})

const rules = {
    name: [{ required: true, message: '请输入海报名称', trigger: ['blur'] }],
    image: [{ required: true, message: '请上传海报图片', trigger: ['change'] }]
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    mode.value == 'edit' ? await posterEdit(formData) : await posterAdd(formData)
    popupRef.value?.close()
    emit('success')
}

const handleClose = () => {
    emit('close')
}

const open = (type = 'add') => {
    mode.value = type
    popupRef.value?.open()
}

const setFormData = (data: Record<any, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            //@ts-ignore
            formData[key] = data[key]
        }
    }
}

defineExpose({
    open,
    setFormData
})
</script>
