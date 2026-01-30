<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    id: {
        type: String,
        default: null,
    },
});

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        :id="id"
        class="ac-input"
        v-model="model"
        ref="input"
    />
</template>
