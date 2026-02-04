<!--
  @fileoverview Botón accesible para abrir menú/volver.
  Solo visible en móvil con focus-visible para accesibilidad.
-->
<template>
  <button
    type="button"
    :aria-label="ariaLabel"
    :class="[
      'lg:hidden flex items-center gap-1.5 font-semibold text-sm',
      'bg-white/90 px-3 py-2 rounded-lg shadow-md border',
      'transition-colors duration-200',
      variantClasses,
      'focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2'
    ]"
    @click="$emit('click')"
  >
    <span aria-hidden="true" class="text-base">{{ icon }}</span>
    <span>{{ label }}</span>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** Texto del botón */
  label: {
    type: String,
    default: 'Ver lista'
  },
  /** Icono del botón */
  icon: {
    type: String,
    default: '☰'
  },
  /** Variante de color */
  variant: {
    type: String,
    default: 'green',
    validator: (v) => ['green', 'blue', 'amber', 'purple'].includes(v)
  },
  /** Etiqueta ARIA */
  ariaLabel: {
    type: String,
    default: 'Abrir menú de selección'
  }
})

defineEmits(['click'])

const variantClasses = computed(() => {
  const variants = {
    green: 'text-green-700 hover:text-green-900 border-green-200 hover:border-green-400 focus-visible:ring-green-500',
    blue: 'text-blue-700 hover:text-blue-900 border-blue-200 hover:border-blue-400 focus-visible:ring-blue-500',
    amber: 'text-amber-700 hover:text-amber-900 border-amber-200 hover:border-amber-400 focus-visible:ring-amber-500',
    purple: 'text-purple-700 hover:text-purple-900 border-purple-200 hover:border-purple-400 focus-visible:ring-purple-500'
  }
  return variants[props.variant] || variants.green
})
</script>
