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

<style scoped>
.ac-input-error {
    font-size: 0.8rem;
    color: var(--ac-danger);
    margin-top: 0.25rem;
}
</style>
