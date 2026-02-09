<script setup>
import { computed } from 'vue'

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (v) => ['primary', 'secondary', 'danger', 'ghost'].includes(v),
    },
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
})

const variantClasses = computed(() => ({
    primary: 'btn-primary',
    secondary: 'btn-secondary',
    danger: 'ac-btn-danger',
    ghost: 'bg-transparent hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-full transition-colors',
})[props.variant])
</script>

<template>
    <button
        :type="type"
        :class="variantClasses"
        :disabled="disabled || loading"
        :aria-busy="loading"
    >
        <span v-if="loading" class="inline-block animate-spin mr-2">&#9696;</span>
        <slot />
    </button>
</template>
