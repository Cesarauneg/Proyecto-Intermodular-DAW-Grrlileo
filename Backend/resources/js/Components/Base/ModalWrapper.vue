<script setup>
import { watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: 'md',
    },
})

const emit = defineEmits(['close'])

const close = () => emit('close')

const onKeydown = (e) => {
    if (e.key === 'Escape' && props.show) {
        close()
    }
}

watch(() => props.show, (val) => {
    document.body.style.overflow = val ? 'hidden' : ''
})

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => {
    document.removeEventListener('keydown', onKeydown)
    document.body.style.overflow = ''
})

const widthClass = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
}
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
            >
                <div class="fixed inset-0 bg-black/50" @click="close" />
                <div
                    class="relative z-10 w-full rounded-xl bg-white shadow-xl"
                    :class="widthClass[maxWidth]"
                >
                    <slot />
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
