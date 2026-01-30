<template>
  <div 
    :class="[
      'rounded-xl sm:rounded-2xl p-4 sm:p-6 border-2 shadow-lg transform hover:scale-105 transition-transform',
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