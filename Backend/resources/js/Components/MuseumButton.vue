<!--
  @fileoverview Botón de museo accesible.
  Permite marcar/desmarcar items como donados al museo.
-->
<template>
  <button
    type="button"
    :class="buttonClasses"
    :aria-label="ariaLabel"
    :aria-pressed="isInMuseum"
    @click="$emit('toggle')"
  >
    <img
      :src="museumIcon"
      alt=""
      aria-hidden="true"
      :class="iconClasses"
    />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** Si el item está en el museo */
  isInMuseum: {
    type: Boolean,
    default: false
  },
  /** URL del icono del museo */
  museumIcon: {
    type: String,
    required: true
  }
})

defineEmits(['toggle'])

// Etiqueta ARIA descriptiva
const ariaLabel = computed(() =>
  props.isInMuseum ? 'Quitar del museo' : 'Agregar al museo'
)

// Clases del botón con estados
const buttonClasses = computed(() => [
  'relative z-50 w-10 h-10 sm:w-12 sm:h-12 rounded-full border-4',
  'transition-all duration-300 transform hover:scale-110 active:scale-95',
  'shadow-lg flex items-center justify-center',
  'focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-amber-500',
  props.isInMuseum
    ? 'bg-gradient-to-br from-amber-400 to-green-500 border-amber-300 hover:from-amber-500 hover:to-orange-600'
    : 'bg-gray-200 border-gray-300 hover:bg-gray-300'
])

// Clases del icono
const iconClasses = computed(() => [
  'w-full h-full p-2 pointer-events-none transition-all duration-300',
  props.isInMuseum ? 'opacity-100' : 'opacity-40'
])
</script>
