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
    <AppButton variant="danger" :type="type">
        <slot />
    </AppButton>
</template>

<style scoped src="@/../css/components/danger-button.css"></style>
