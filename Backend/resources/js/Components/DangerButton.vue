<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="ac-btn-danger">
        <slot />
    </button>
</template>

<style scoped src="@/../css/components/danger-button.css"></style>
