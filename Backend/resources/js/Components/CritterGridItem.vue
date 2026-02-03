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
    <!-- Indicador de disponibilidad (solo si aplica) -->
    <div 
      v-if="isAvailable" 
      class="absolute top-1 right-1 w-3 h-3 bg-green-500 rounded-full border border-white shadow z-10"
    ></div>
    
    <!-- Imagen miniatura - usa icon si existe, si no usa image -->
    <img
      :src="item.icon || item.image"
      :alt="displayName"
      class="w-full h-12 sm:h-14 lg:h-16 object-contain mb-1"
    />
    
    <!-- Nombre - intenta name_es, luego name_en, luego name -->
    <p class="text-xs text-center truncate font-medium">{{ capitalize(displayName) }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { capitalize } from '@/Utils/formatters.js' 

const props = defineProps({
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

// Computed para obtener el nombre correcto
const displayName = computed(() => {
  return props.item.name_es || props.item.name_en || props.item.name || 'Sin nombre'
})
</script>