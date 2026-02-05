<script setup>
const props = defineProps({
    clickable: {
        type: Boolean,
        default: false,
    },
    ariaLabel: {
        type: String,
        default: '',
    },
    colorClasses: {
        type: String,
        default: 'bg-white border border-gray-200',
    },
})

const emit = defineEmits(['click'])

const handleKeydown = (e) => {
    if (props.clickable && (e.key === 'Enter' || e.key === ' ')) {
        e.preventDefault()
        emit('click')
    }
}
</script>

<template>
    <component
        :is="clickable ? 'button' : 'div'"
        :class="['rounded-xl shadow-sm overflow-hidden transition-all duration-200', colorClasses, { 'cursor-pointer hover:shadow-md': clickable }]"
        :aria-label="ariaLabel || undefined"
        :tabindex="clickable ? 0 : undefined"
        @click="clickable ? emit('click') : null"
        @keydown="handleKeydown"
    >
        <slot />
    </component>
</template>
