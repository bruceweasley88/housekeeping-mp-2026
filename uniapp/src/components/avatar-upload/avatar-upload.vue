<template>
    <button
        class="avatar-upload p-0 m-0 rounded"
        :style="styles"
        hover-class="none"
        open-type="chooseAvatar"
        @click="chooseAvatar"
        @chooseavatar="chooseAvatar"
    >
        <image class="w-full h-full" mode="heightFix" :src="modelValue" v-if="modelValue" />
        <slot v-else>
            <div
                :style="styles"
                class="border border-dotted border-light flex w-full h-full flex-col items-center justify-center text-muted text-xs box-border rounded"
            >
                <u-icon name="plus" :size="36" />
                添加图片
            </div>
        </slot>
    </button>
</template>
<script lang="ts" setup>
import { uploadImage } from '@/api/app'
import { useUserStore } from '@/stores/user'
import { addUnit } from '@/utils/util'
import { isBoolean } from 'lodash-es'
import { computed, CSSProperties, onUnmounted } from 'vue'

const props = defineProps({
    modelValue: {
        type: String
    },
    fileKey: {
        type: String,
        default: 'uri'
    },
    size: {
        type: [String, Number],
        default: 120
    },
    round: {
        type: [Boolean, String, Number],
        default: false
    },
    border: {
        type: Boolean,
        default: true
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()
const userStore = useUserStore()
const styles = computed<CSSProperties>(() => {
    const size = addUnit(props.size)
    return {
        width: size,
        height: size,
        borderRadius: isBoolean(props.round) ? (props.round ? '50%' : '') : addUnit(props.round)
    }
})

const chooseAvatar = (e: any) => {
    console.log('=== chooseAvatar 被触发 ===', e)
    // #ifndef MP-WEIXIN
    console.log('非小程序环境，跳转到裁剪页面')
    uni.navigateTo({
        url: '/uni_modules/vk-uview-ui/components/u-avatar-cropper/u-avatar-cropper?destWidth=300&rectWidth=200&fileType=jpg'
    })
    // #endif
    // #ifdef MP-WEIXIN
    console.log('小程序环境，获取 avatarUrl')
    const path = e.detail?.avatarUrl
    console.log('avatarUrl:', path)
    if (path) {
        uploadImageIng(path)
    } else {
        console.log('没有获取到 avatarUrl')
    }
    // #endif
}

const uploadImageIng = async (file: string) => {
    console.log('=== uploadImageIng 被调用 ===', file)
    uni.showLoading({
        title: '正在上传中...'
    })
    try {
        console.log('开始上传图片，temToken:', userStore.temToken)
        const res: any = await uploadImage(file, userStore.temToken!)
        uni.hideLoading()
        console.log('上传成功，返回结果:', res)
        console.log('fileKey:', props.fileKey, '值:', res[props.fileKey])
        emit('update:modelValue', res[props.fileKey])
    } catch (error) {
        uni.hideLoading()
        console.error('上传失败:', error)
        uni.$u.toast(error)
    }
}
// 监听从裁剪页发布的事件，获得裁剪结果
uni.$on('uAvatarCropper', (path) => {
    uploadImageIng(path)
})
onUnmounted(() => {
    uni.$off('uAvatarCropper')
})
</script>

<style lang="scss" scoped>
.avatar-upload {
    background: #fff;
    overflow: hidden;
    &::after {
        border: none;
    }
}
</style>
