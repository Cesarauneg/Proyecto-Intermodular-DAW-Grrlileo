<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="ac-input-error">{{ message }}</p>
    </div>
</template>

<style scoped src="@/../css/components/input-error.css"></style>
