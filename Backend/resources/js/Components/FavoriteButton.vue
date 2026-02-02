<template>
  <button 
    @click.stop="$emit('toggle')" 
    class="p-2 rounded-full transition-all duration-300"
    :class="active ? 'text-red-500 scale-110' : 'text-gray-300 hover:text-red-400'"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :fill="active ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
  </button>
</template>

<script setup>
defineProps({ active: Boolean })
defineEmits(['toggle'])
</script>