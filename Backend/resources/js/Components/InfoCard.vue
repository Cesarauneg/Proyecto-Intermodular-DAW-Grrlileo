<template>
  <div
    :class="[
      'rounded-lg sm:rounded-xl p-1.5 sm:p-2.5 border-2 shadow-md transform hover:scale-[1.02] transition-transform',
      colorClasses
    ]"
  >
    <slot></slot>
  </div>
</template>

<script setup>
defineProps({
  colorClasses: {
    type: String,
    default: 'bg-gradient-to-r from-gray-100 to-gray-200 border-gray-400'
  }
})
</script>