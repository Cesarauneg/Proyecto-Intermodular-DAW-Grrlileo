<script setup>
import AppButton from '@/Components/Base/AppButton.vue'

defineProps({
    type: {
        type: String,
        default: 'button',
    },
})
</script>

<template>
    <AppButton variant="primary" :type="type">
        <slot />
    </AppButton>
</template>
