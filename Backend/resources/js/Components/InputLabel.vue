<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="ac-label">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>

<style scoped>
.ac-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ac-text-primary);
}
</style>
