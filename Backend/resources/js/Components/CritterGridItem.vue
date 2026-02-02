<template>
  <div
    @click="$emit('select', item)"
    :class="[
      'cursor-pointer p-2 rounded-lg transition-all duration-200 relative',
      isSelected 
        ? 'bg-green-200 border-2 border-green-500 shadow-lg' 
        : 'bg-white border-2 border-gray-200 hover:border-green-400 hover:shadow-md'
    ]"
  >
    <!-- Indicador de disponibilidad -->
    <div 
      v-if="isAvailable" 
      class="absolute top-1 right-1 w-3 h-3 bg-green-500 rounded-full border border-white shadow z-10"
    ></div>
    
    <!-- Imagen miniatura -->
    <img
      :src="item.icon"
      :alt="item.name_es"
      class="w-full h-12 sm:h-14 lg:h-16 object-contain mb-1"
    />
    
    <!-- Nombre -->
    <p class="text-xs text-center truncate font-medium">{{ capitalize(item.name_es) }}</p>
  </div>
</template>

<script setup>
import { capitalize } from '@/Utils/formatters.js' 
defineProps({
  item: {
    type: Object,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  },
  isAvailable: {
    type: Boolean,
    default: false
  }
})

defineEmits(['select'])
</script>