<script setup>
defineProps({
    value: {
        type: String,
    },
    for: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <label :for="$props.for" class="ac-label">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>

<style scoped src="@/../css/components/input-label.css"></style>
