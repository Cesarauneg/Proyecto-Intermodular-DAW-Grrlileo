<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },
    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="ac-checkbox"
    />
</template>

<style scoped>
.ac-checkbox {
    border-radius: 0.25rem;
    border: 2px solid var(--ac-input-border);
    color: var(--ac-green);
    width: 1rem;
    height: 1rem;
    cursor: pointer;
}
.ac-checkbox:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(109, 186, 109, 0.25);
}
.ac-checkbox:checked {
    background-color: var(--ac-green);
    border-color: var(--ac-green-dark);
}
</style>
