<template>
    <view class="image-upload">
        <!-- 已上传图片列表 -->
        <view
            v-for="(item, index) in modelValue"
            :key="index"
            class="image-item"
        >
            <image class="image-preview" :src="item" mode="aspectFill" />
            <view v-if="!disabled" class="image-delete" @click="handleDelete(index)">
                <text class="delete-icon">×</text>
            </view>
        </view>

        <!-- 上传按钮 -->
        <view
            v-if="modelValue.length < maxCount && !disabled"
            class="upload-btn"
            @click="handleUpload"
        >
            <image class="upload-icon" :src="iconPath" mode="aspectFit" />
            <text class="upload-text">立即上传</text>
        </view>
    </view>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { uploadImage } from '@/api/app'
import { useUserStore } from '@/stores/user'

const props = withDefaults(defineProps<{
    modelValue: string[]
    maxCount?: number
    icon?: string
    disabled?: boolean
}>(), {
    maxCount: 9,
    disabled: false
})

const emit = defineEmits<{
    (event: 'update:modelValue', value: string[]): void
    (event: 'delete', index: number): void
}>()

const userStore = useUserStore()
const iconPath = computed(() => props.icon || '/static/images/img_upload.png')

const handleUpload = () => {
    const remain = props.maxCount - props.modelValue.length
    if (remain <= 0) {
        uni.$u.toast(`最多上传${props.maxCount}张`)
        return
    }

    uni.chooseImage({
        count: remain,
        sizeType: ['compressed'],
        sourceType: ['album', 'camera'],
        success: async (res) => {
            uni.showLoading({ title: '上传中...' })
            try {
                const newImages: string[] = []
                for (const path of res.tempFilePaths) {
                    const result = await uploadImage(path, userStore.token || undefined)
                    if (result?.uri) {
                        newImages.push(result.uri)
                    }
                }
                emit('update:modelValue', [...props.modelValue, ...newImages])
            } catch (e) {
                uni.$u.toast('上传失败')
            } finally {
                uni.hideLoading()
            }
        }
    })
}

const handleDelete = (index: number) => {
    const newList = [...props.modelValue]
    newList.splice(index, 1)
    emit('update:modelValue', newList)
    emit('delete', index)
}
</script>

<style lang="scss" scoped>
.image-upload {
    display: flex;
    flex-wrap: wrap;
    gap: 15rpx;
}

.image-item {
    width: 146rpx;
    height: 146rpx;
    border-radius: 15rpx;
    overflow: hidden;
    position: relative;
}

.image-preview {
    width: 100%;
    height: 100%;
}

.image-delete {
    position: absolute;
    top: 0;
    right: 0;
    width: 36rpx;
    height: 36rpx;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0 0 0 15rpx;
}

.delete-icon {
    color: #fff;
    font-size: 28rpx;
    line-height: 1;
}

.upload-btn {
    width: 146rpx;
    height: 146rpx;
    background: #ffffff;
    border-radius: 15rpx;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.upload-icon {
    width: 54rpx;
    height: 44rpx;
}

.upload-text {
    font-size: 25rpx;
    font-weight: 500;
    color: #444444;
    margin-top: 12rpx;
}
</style>
