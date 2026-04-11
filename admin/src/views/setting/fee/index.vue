<!-- 手续费设置 -->
<template>
    <div class="fee-settings">
        <el-form ref="formRef" :model="formData" :rules="rules" label-width="120px">
            <div class="flex gap-4">
                <el-card shadow="never" class="!border-none flex-1">
                    <div class="text-xl font-medium mb-[20px]">紧急手续费</div>
                    <el-form-item label="费率" prop="urgent_fee_rate">
                        <div class="w-60">
                            <el-input-number
                                v-model="formData.urgent_fee_rate"
                                :min="0"
                                :max="100"
                                :precision="2"
                                :step="0.5"
                                controls-position="right"
                            />
                            <span class="ml-2 text-gray-500">%</span>
                        </div>
                    </el-form-item>
                </el-card>
                <el-card shadow="never" class="!border-none flex-1">
                    <div class="text-xl font-medium mb-[20px]">提现手续费</div>
                    <el-form-item label="费率" prop="withdraw_fee_rate">
                        <div class="w-60">
                            <el-input-number
                                v-model="formData.withdraw_fee_rate"
                                :min="0"
                                :max="100"
                                :precision="2"
                                :step="0.5"
                                controls-position="right"
                            />
                            <span class="ml-2 text-gray-500">%</span>
                        </div>
                    </el-form-item>
                </el-card>
            </div>
        </el-form>
        <footer-btns v-perms="['setting.fee_settings/setConfig']">
            <el-button type="primary" @click="handleSubmit">保存</el-button>
        </footer-btns>
    </div>
</template>

<script lang="ts" setup name="feeSettings">
import type { FormInstance } from 'element-plus'
import { getFeeSettings, setFeeSettings } from '@/api/setting/fee'

const formRef = ref<FormInstance>()

const formData = reactive({
    urgent_fee_rate: 3,
    withdraw_fee_rate: 3,
})

const rules = {
    urgent_fee_rate: [
        { required: true, message: '请输入紧急手续费率', trigger: 'blur' },
    ],
    withdraw_fee_rate: [
        { required: true, message: '请输入提现手续费率', trigger: 'blur' },
    ],
}

const getData = async () => {
    const data = await getFeeSettings()
    formData.urgent_fee_rate = data.urgent_fee_rate
    formData.withdraw_fee_rate = data.withdraw_fee_rate
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    await setFeeSettings(formData)
    getData()
}

getData()
</script>

<style lang="scss" scoped></style>
