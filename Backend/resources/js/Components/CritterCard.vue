<template>
  <article
    :class="cardClasses"
    role="button"
    tabindex="0"
    :aria-label="`Ver detalles de ${capitalize(critter.name_es)}`"
    @click="$emit('select', critter)"
    @keydown.enter="$emit('select', critter)"
    @keydown.space.prevent="$emit('select', critter)"
  >
    <!-- Header con gradiente -->
    <header class="bg-gradient-to-r from-green-400 to-blue-400 px-4 py-3 text-center relative">
      <!-- Badge de urgencia -->
      <span
        v-if="urgent"
        class="absolute top-2 left-2 px-2 py-1 rounded-full text-xs font-bold bg-red-500 text-white animate-pulse shadow-lg"
        role="status"
        aria-live="polite"
      >
        ¡Ultimo mes!
      </span>

      <!-- Badge de tipo -->
      <span
        class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-semibold bg-white/90 shadow"
        aria-label="Tipo de criatura"
      >
        {{ typeLabel }}
      </span>

      <h3 class="font-bold text-white text-lg drop-shadow-md pt-4">
        {{ capitalize(critter.name_es) }}
      </h3>
    </header>

    <!-- Contenido principal -->
    <div class="p-4">
      <!-- Imagen centrada -->
      <figure class="flex justify-center mb-4">
        <div class="relative">
          <div
            class="absolute inset-0 bg-green-200 rounded-full blur-xl opacity-50"
            aria-hidden="true"
          ></div>
          <img
            :src="critter.icon"
            :alt="`Icono de ${critter.name_es}`"
            class="relative w-20 h-20 object-contain drop-shadow-lg transform hover:scale-110 transition-transform duration-300"
            loading="lazy"
            decoding="async"
          />
        </div>
        <figcaption class="sr-only">{{ critter.name_es }}</figcaption>
      </figure>

      <!-- Info cards con lista de definiciones -->
      <dl class="space-y-2">
        <!-- Precio -->
        <div class="flex items-center justify-between bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg px-3 py-2 border border-yellow-200">
          <dt class="text-sm font-semibold text-yellow-800">
            <span aria-hidden="true">💰</span> Precio
          </dt>
          <dd class="font-bold text-yellow-900">{{ formatPrice(critter.price) }}</dd>
        </div>

        <!-- Ubicacion -->
        <div class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg px-3 py-2 border border-blue-200">
          <dt class="text-sm font-semibold text-blue-800">
            <span aria-hidden="true">📍</span> Ubicacion
          </dt>
          <dd class="font-medium text-blue-900 text-right text-sm truncate max-w-[140px]">
            {{ critter.location || defaultLocation }}
          </dd>
        </div>

        <!-- Horario -->
        <div class="flex items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg px-3 py-2 border border-purple-200">
          <dt class="text-sm font-semibold text-purple-800">
            <span aria-hidden="true">🕐</span> Horario
          </dt>
          <dd class="font-medium text-purple-900 text-sm">{{ timeDisplay }}</dd>
        </div>
      </dl>
    </div>
  </article>
</template>

<script setup>
/**
 * @fileoverview Tarjeta de criatura para la Critterpedia.
 * Muestra información resumida de peces, bichos o criaturas marinas.
 *
 * @description Componente reutilizable que presenta una criatura con su
 * imagen, precio, ubicación y horario de disponibilidad.
 */

import { computed } from 'vue'
import { capitalize } from '@/Utils/formatters.js'

/**
 * @typedef {Object} Critter
 * @property {number} id - ID único
 * @property {string} name_es - Nombre en español
 * @property {string} icon - URL del icono
 * @property {number} price - Precio en bayas
 * @property {string} [location] - Ubicación donde encontrarla
 * @property {number[]} [time_array] - Horas disponibles
 * @property {boolean} [is_all_day] - Si está disponible todo el día
 */

const props = defineProps({
  /** @type {Critter} Datos de la criatura */
  critter: {
    type: Object,
    required: true
  },
  /** Tipo de criatura: fish, bug o sea */
  type: {
    type: String,
    default: 'fish',
    validator: (v) => ['fish', 'bug', 'sea'].includes(v)
  },
  /** Indica si la criatura se va pronto (último mes disponible) */
  urgent: {
    type: Boolean,
    default: false
  }
})

defineEmits(['select'])

/**
 * Clases CSS dinámicas para el contenedor principal
 */
const cardClasses = computed(() => [
  'relative overflow-hidden rounded-2xl transition-all duration-300 cursor-pointer',
  'bg-white/90 backdrop-blur shadow-lg hover:shadow-2xl hover:-translate-y-2',
  'border-4 focus:outline-none focus:ring-4 focus:ring-offset-2',
  props.urgent
    ? 'border-red-400 hover:border-red-500 focus:ring-red-300'
    : 'border-green-300 hover:border-green-400 focus:ring-green-300'
])

/**
 * Etiqueta del tipo de criatura con emoji
 */
const typeLabel = computed(() => {
  const labels = {
    fish: '🐟 Pez',
    bug: '🦋 Bicho',
    sea: '🦑 Marino'
  }
  return labels[props.type] || '🐟 Pez'
})

/**
 * Ubicación por defecto según el tipo de criatura
 */
const defaultLocation = computed(() => {
  return props.type === 'sea' ? 'Buceo' : 'Desconocido'
})

/**
 * Texto de disponibilidad horaria
 */
const timeDisplay = computed(() => {
  if (props.critter.is_all_day) return 'Todo el dia'
  return formatTimeRange(props.critter.time_array)
})

/**
 * Formatea el precio con separador de miles
 * @param {number} price - Precio en bayas
 * @returns {string} Precio formateado
 */
const formatPrice = (price) => {
  if (!price) return 'N/A'
  return new Intl.NumberFormat('es-ES').format(price) + ' bells'
}

/**
 * Formatea un array de horas en un rango legible
 * @param {number[]} timeArray - Array de horas (0-23)
 * @returns {string} Rango de horas formateado
 */
const formatTimeRange = (timeArray) => {
  if (!timeArray || !Array.isArray(timeArray) || timeArray.length === 0) {
    return 'Horario desconocido'
  }

  const validHours = timeArray
    .map(h => parseInt(h, 10))
    .filter(h => !isNaN(h) && h >= 0 && h <= 23)

  if (validHours.length === 0) return 'Horario desconocido'

  /**
   * Convierte hora 24h a formato 12h
   * @param {number} h - Hora en formato 24h
   * @returns {string}
   */
  const formatHour = (h) => {
    const hour = h % 24
    if (hour === 0) return '12 AM'
    if (hour === 12) return '12 PM'
    if (hour < 12) return `${hour} AM`
    return `${hour - 12} PM`
  }

  const sorted = [...validHours].sort((a, b) => a - b)

  // Detectar si cruza medianoche (hay un gap en las horas)
  let gapStart = -1
  let gapEnd = -1
  for (let i = 0; i < sorted.length - 1; i++) {
    if (sorted[i + 1] - sorted[i] > 1) {
      gapStart = sorted[i]
      gapEnd = sorted[i + 1]
      break
    }
  }

  if (gapStart !== -1) {
    return `${formatHour(gapEnd)} - ${formatHour((gapStart + 1) % 24)}`
  }

  const min = sorted[0]
  const max = sorted[sorted.length - 1]
  return `${formatHour(min)} - ${formatHour((max + 1) % 24)}`
}
</script>
