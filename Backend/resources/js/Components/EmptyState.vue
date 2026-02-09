<template>
  <div class="text-center">
    <div class="text-6xl sm:text-7xl lg:text-8xl mb-4">{{ icon }}</div>
    <p class="text-2xl sm:text-2xl lg:text-3xl text-gray-400 font-medium">{{ title }}</p>
    <p class="text-lg sm:text-xl text-gray-300 mt-2">{{ subtitle }}</p>
  </div>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    default: '🦋'
  },
  title: {
    type: String,
    default: 'Selecciona un bicho'
  },
  subtitle: {
    type: String,
    default: 'para ver su información'
  }
})
</script>