<!--
  @fileoverview Tarjeta de estadística reutilizable con variantes de color.
  Reemplaza el patrón repetitivo de InfoCard + contenido.
-->
<template>
  <div
    :class="[
      'rounded-lg sm:rounded-xl p-1.5 sm:p-2.5 border-2 shadow-md',
      'transform hover:scale-[1.02] transition-transform',
      cardClasses
    ]"
    role="listitem"
  >
    <div class="flex items-center justify-between gap-1">
      <!-- Label con icono -->
      <span :class="['text-xs sm:text-xs lg:text-sm font-bold', labelClasses]">
        <span aria-hidden="true">{{ icon }}</span>
        {{ label }}
      </span>

      <!-- Valor -->
      <span :class="['text-xs sm:text-sm lg:text-base font-bold text-right', valueClasses]">
        <slot>{{ formattedValue }}</slot>
      </span>
    </div>

    <!-- Contenido adicional (para tarjetas compuestas como Rareza/Estación) -->
    <slot name="extra" />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** Emoji o icono */
  icon: {
    type: String,
    required: true
  },
  /** Texto de la etiqueta */
  label: {
    type: String,
    required: true
  },
  /** Valor a mostrar */
  value: {
    type: [String, Number],
    default: ''
  },
  /** Variante de color */
  variant: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'yellow', 'blue', 'green', 'red', 'purple', 'amber', 'gray'].includes(v)
  },
  /** Si el valor es un número, formatearlo */
  formatNumber: {
    type: Boolean,
    default: false
  }
})

// Configuración de variantes
const variantConfig = {
  yellow: {
    card: 'bg-gradient-to-r from-yellow-100 to-yellow-200 border-yellow-400',
    label: 'text-yellow-800',
    value: 'text-yellow-900'
  },
  blue: {
    card: 'bg-gradient-to-r from-blue-100 to-cyan-200 border-blue-400',
    label: 'text-blue-800',
    value: 'text-blue-900'
  },
  green: {
    card: 'bg-gradient-to-r from-green-100 to-emerald-200 border-green-400',
    label: 'text-green-800',
    value: 'text-green-900'
  },
  red: {
    card: 'bg-gradient-to-r from-red-100 to-orange-200 border-red-400',
    label: 'text-red-800',
    value: 'text-red-900'
  },
  purple: {
    card: 'bg-gradient-to-r from-purple-100 to-pink-200 border-purple-400',
    label: 'text-purple-800',
    value: 'text-purple-900'
  },
  amber: {
    card: 'bg-gradient-to-r from-amber-100 to-yellow-200 border-amber-400',
    label: 'text-amber-800',
    value: 'text-amber-900'
  },
  gray: {
    card: 'bg-gradient-to-r from-gray-100 to-gray-200 border-gray-400',
    label: 'text-gray-800',
    value: 'text-gray-900'
  },
  default: {
    card: 'bg-gradient-to-r from-gray-100 to-gray-200 border-gray-400',
    label: 'text-gray-800',
    value: 'text-gray-900'
  }
}

const config = computed(() => variantConfig[props.variant] || variantConfig.default)
const cardClasses = computed(() => config.value.card)
const labelClasses = computed(() => config.value.label)
const valueClasses = computed(() => config.value.value)

const formattedValue = computed(() => {
  if (props.formatNumber && typeof props.value === 'number') {
    return props.value.toLocaleString('es-ES')
  }
  return props.value
})
</script>
