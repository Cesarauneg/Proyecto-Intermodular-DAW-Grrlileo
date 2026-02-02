<template>
  <article
    :class="[
      'relative overflow-hidden rounded-2xl transition-all duration-300 cursor-pointer',
      'bg-white/90 backdrop-blur shadow-lg hover:shadow-2xl hover:-translate-y-2',
      'border-4',
      urgent
        ? 'border-red-400 hover:border-red-500'
        : 'border-green-300 hover:border-green-400'
    ]"
    @click="$emit('select', critter)"
  >
    <!-- Header con gradiente -->
    <div class="bg-gradient-to-r from-green-400 to-emerald-400 px-4 py-3 text-center relative">
      <!-- Badge de urgencia -->
      <span
        v-if="urgent"
        class="absolute top-2 left-2 px-2 py-1 rounded-full text-xs font-bold bg-red-500 text-white animate-pulse shadow-lg"
      >
        ¡Ultimo mes!
      </span>

      <!-- Badge de tipo -->
      <span class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-semibold bg-white/90 shadow">
        {{ typeLabel }}
      </span>

      <h3 class="font-bold text-white text-lg drop-shadow-md pt-4">
        {{ capitalize(critter.name_es) }}
      </h3>
    </div>

    <!-- Contenido principal -->
    <div class="p-4">
      <!-- Imagen centrada -->
      <div class="flex justify-center mb-4">
        <div class="relative">
          <div class="absolute inset-0 bg-green-200 rounded-full blur-xl opacity-50"></div>
          <img
            :src="critter.icon"
            :alt="critter.name_es"
            class="relative w-20 h-20 object-contain drop-shadow-lg transform hover:scale-110 transition-transform duration-300"
            loading="lazy"
          />
        </div>
      </div>

      <!-- Info cards -->
      <div class="space-y-2">
        <!-- Precio -->
        <div class="flex items-center justify-between bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg px-3 py-2 border border-yellow-200">
          <span class="text-sm font-semibold text-yellow-800">💰 Precio</span>
          <span class="font-bold text-yellow-900">{{ formatPrice(critter.price) }}</span>
        </div>

        <!-- Ubicacion -->
        <div class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg px-3 py-2 border border-blue-200">
          <span class="text-sm font-semibold text-blue-800">📍 Ubicacion</span>
          <span class="font-medium text-blue-900 text-right text-sm truncate max-w-[140px]">{{ critter.location || defaultLocation }}</span>
        </div>

        <!-- Horario -->
        <div class="flex items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg px-3 py-2 border border-purple-200">
          <span class="text-sm font-semibold text-purple-800">🕐 Horario</span>
          <span class="font-medium text-purple-900 text-sm">{{ timeDisplay }}</span>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { capitalize } from '@/Utils/formatters.js'

const props = defineProps({
  critter: {
    type: Object,
    required: true
  },
  type: {
    type: String,
    default: 'fish',
    validator: (v) => ['fish', 'bug', 'sea'].includes(v)
  },
  urgent: {
    type: Boolean,
    default: false
  }
})

defineEmits(['select'])

const typeLabel = computed(() => {
  const labels = {
    fish: '🐟 Pez',
    bug: '🦋 Bicho',
    sea: '🦑 Marino'
  }
  return labels[props.type] || '🐟 Pez'
})

const defaultLocation = computed(() => {
  return props.type === 'sea' ? 'Buceo' : 'Desconocido'
})

const timeDisplay = computed(() => {
  if (props.critter.is_all_day) return 'Todo el dia'
  return formatTimeRange(props.critter.time_array)
})

const formatPrice = (price) => {
  if (!price) return 'N/A'
  return new Intl.NumberFormat('es-ES').format(price) + ' bells'
}

const formatTimeRange = (timeArray) => {
  if (!timeArray || !Array.isArray(timeArray) || timeArray.length === 0) {
    return 'Horario desconocido'
  }

  const validHours = timeArray
    .map(h => parseInt(h, 10))
    .filter(h => !isNaN(h) && h >= 0 && h <= 23)

  if (validHours.length === 0) return 'Horario desconocido'

  const formatHour = (h) => {
    const hour = h % 24
    if (hour === 0) return '12 AM'
    if (hour === 12) return '12 PM'
    if (hour < 12) return `${hour} AM`
    return `${hour - 12} PM`
  }

  const sorted = [...validHours].sort((a, b) => a - b)

  // Detectar si cruza medianoche
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
