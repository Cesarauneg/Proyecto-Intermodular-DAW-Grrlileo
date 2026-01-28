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

<style scoped src="@/../css/components/input-label.css"></style>
