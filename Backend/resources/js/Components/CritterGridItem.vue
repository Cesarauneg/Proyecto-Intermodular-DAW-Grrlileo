<!--
  @fileoverview Item individual del grid de criaturas.
  Botón accesible con estados visuales para selección y disponibilidad.
-->
<template>
  <button
    type="button"
    :class="itemClasses"
    :aria-label="`${displayName}${isAvailable ? ', disponible ahora' : ''}${isSelected ? ', seleccionado' : ''}`"
    :aria-pressed="isSelected"
    @click="$emit('select', item)"
  >
    <!-- Indicador de disponibilidad -->
    <span
      v-if="isAvailable"
      class="absolute top-1 right-1 w-3 h-3 bg-green-500 rounded-full border border-white shadow z-10"
      aria-hidden="true"
    />

    <!-- Imagen miniatura -->
    <img
      :src="item.icon || item.image"
      :alt=""
      aria-hidden="true"
      class="w-full h-12 sm:h-14 lg:h-16 object-contain mb-1"
      loading="lazy"
    />

    <!-- Nombre -->
    <p class="text-xs text-center truncate font-medium">
      {{ capitalize(displayName) }}
    </p>
  </button>
</template>

<script setup>
import { computed } from 'vue'
import { capitalize } from '@/Utils/formatters.js'

const props = defineProps({
  /** Objeto con los datos del item */
  item: {
    type: Object,
    required: true
  },
  /** Si el item está seleccionado */
  isSelected: {
    type: Boolean,
    default: false
  },
  /** Si el item está disponible actualmente */
  isAvailable: {
    type: Boolean,
    default: false
  }
})

defineEmits(['select'])

// Nombre a mostrar
const displayName = computed(() =>
  props.item.name_es || props.item.name_en || props.item.name || 'Sin nombre'
)

// Clases del item con estados
const itemClasses = computed(() => [
  'cursor-pointer p-2 rounded-lg transition-all duration-200 relative w-full',
  'focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2',
  props.isSelected
    ? 'bg-green-200 border-2 border-green-500 shadow-lg'
    : 'bg-white border-2 border-gray-200 hover:border-green-400 hover:shadow-md'
])
</script>
