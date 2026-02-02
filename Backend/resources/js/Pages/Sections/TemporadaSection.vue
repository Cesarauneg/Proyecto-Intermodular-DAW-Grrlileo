<template>
  <section class="w-full px-4 py-6" aria-labelledby="temporada-title">
    <!-- Header -->
    <header class="text-center mb-8">
      <h2 id="temporada-title" class="text-4xl font-extrabold text-green-800">
        Temporada
      </h2>
      <p class="text-gray-500 mt-2">
        {{ monthName }} {{ currentYear }} - Hemisferio Norte
      </p>
    </header>

    <!-- Pestanas de categoria temporal -->
    <nav class="flex justify-center mb-6" role="tablist" aria-label="Categorias de disponibilidad">
      <div class="inline-flex rounded-xl bg-green-100 p-1 shadow-inner">
        <button
          v-for="tab in timeTabs"
          :key="tab.key"
          role="tab"
          :aria-selected="activeTimeTab === tab.key"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
            activeTimeTab === tab.key
              ? 'bg-white text-green-700 shadow-md'
              : 'text-green-600 hover:text-green-800 hover:bg-green-50'
          ]"
          @click="activeTimeTab = tab.key"
        >
          <span class="mr-1">{{ tab.icon }}</span>
          {{ tab.label }}
          <span
            v-if="getCountForTab(tab.key) > 0"
            :class="[
              'ml-2 px-2 py-0.5 rounded-full text-xs',
              tab.key === 'leaving' ? 'bg-red-100 text-red-600' : 'bg-green-200 text-green-700'
            ]"
          >
            {{ getCountForTab(tab.key) }}
          </span>
        </button>
      </div>
    </nav>

    <!-- Filtros por tipo de criatura -->
    <div class="flex justify-center gap-2 mb-8 flex-wrap">
      <button
        v-for="filter in typeFilters"
        :key="filter.key"
        :class="[
          'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2',
          activeTypeFilter === filter.key
            ? 'bg-green-600 text-white border-green-600 shadow-md'
            : 'bg-white text-green-700 border-green-300 hover:border-green-500 hover:bg-green-50'
        ]"
        @click="activeTypeFilter = filter.key"
      >
        <span class="mr-1">{{ filter.icon }}</span>
        {{ filter.label }}
      </button>
    </div>

    <!-- Mensaje informativo para "Ultima Oportunidad" -->
    <div
      v-if="activeTimeTab === 'leaving' && filteredCritters.length > 0"
      class="max-w-2xl mx-auto mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-center"
    >
      <p class="text-red-700 font-medium">
        <span class="text-lg">⚠️</span>
        Estas criaturas no estaran disponibles a partir del proximo mes. ¡Atrapalas antes de que se vayan!
      </p>
    </div>

    <!-- Grid de criaturas -->
    <div
      v-if="filteredCritters.length > 0"
      class="grid gap-6"
      style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));"
    >
      <CritterCard
        v-for="critter in filteredCritters"
        :key="`${critter.type}-${critter.id}`"
        :critter="critter"
        :type="critter.type"
        :urgent="activeTimeTab === 'leaving'"
      />
    </div>

    <!-- Estado vacio -->
    <div
      v-else
      class="text-center py-16"
    >
      <div class="text-6xl mb-4">
        {{ activeTimeTab === 'leaving' ? '🎉' : '🔍' }}
      </div>
      <p class="text-gray-500 text-lg">
        {{ emptyMessage }}
      </p>
    </div>

    <!-- Resumen de temporada -->
    <aside
      v-if="filteredCritters.length > 0"
      class="mt-8 p-4 bg-green-50 rounded-xl border border-green-200"
    >
      <h4 class="font-semibold text-green-800 mb-2">Resumen de {{ monthName }}</h4>
      <div class="flex flex-wrap gap-4 text-sm text-green-700">
        <span>🟢 Disponibles ahora: {{ availableNow.length }}</span>
        <span>🔴 Se van este mes: {{ leavingThisMonth.length }}</span>
        <span>🆕 Recien llegados: {{ newArrivals.length }}</span>
      </div>
    </aside>
  </section>
</template>

<script setup>
/**
 * @fileoverview Seccion de Temporada para la Critterpedia.
 * Muestra criaturas filtradas por disponibilidad temporal.
 */

import { ref, computed } from 'vue'
import CritterCard from '@/Components/CritterCard.vue'

const props = defineProps({
  fish: {
    type: Array,
    default: () => []
  },
  bugs: {
    type: Array,
    default: () => []
  },
  seaCreatures: {
    type: Array,
    default: () => []
  }
})

// Constantes de tiempo
const currentYear = new Date().getFullYear()
const currentMonth = new Date().getMonth() + 1 // 1-12
const currentHour = new Date().getHours()

const monthNames = [
  'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
]
const monthName = monthNames[currentMonth - 1]

// Estado reactivo
const activeTimeTab = ref('available')
const activeTypeFilter = ref('all')

// Configuracion de pestanas
const timeTabs = [
  { key: 'available', label: 'Disponibles', icon: '🟢' },
  { key: 'leaving', label: 'Se van', icon: '🔴' },
  { key: 'new', label: 'Nuevos', icon: '🆕' }
]

const typeFilters = [
  { key: 'all', label: 'Todos', icon: '🌿' },
  { key: 'fish', label: 'Peces', icon: '🐟' },
  { key: 'bugs', label: 'Bichos', icon: '🦋' },
  { key: 'sea', label: 'Criaturas marinas', icon: '🦑' }
]

/**
 * Combina peces, bichos y criaturas marinas en un array unificado con tipo
 */
const allCritters = computed(() => {
  const fishWithType = props.fish.map(f => ({ ...f, type: 'fish' }))
  const bugsWithType = props.bugs.map(b => ({ ...b, type: 'bug' }))
  const seaWithType = props.seaCreatures.map(s => ({ ...s, type: 'sea' }))
  return [...fishWithType, ...bugsWithType, ...seaWithType]
})

/**
 * Verifica si una criatura esta disponible en el mes y hora actual
 */
const isAvailableNow = (critter) => {
  const monthArray = critter.month_array_northern || []
  const timeArray = critter.time_array || []

  if (!monthArray.includes(currentMonth)) return false

  if (!critter.is_all_day && timeArray.length > 0) {
    if (!timeArray.includes(currentHour)) return false
  }

  return true
}

/**
 * Verifica si la criatura se va este mes
 */
const isLeavingThisMonth = (critter) => {
  const monthArray = critter.month_array_northern || []
  const nextMonth = currentMonth === 12 ? 1 : currentMonth + 1
  return monthArray.includes(currentMonth) && !monthArray.includes(nextMonth)
}

/**
 * Verifica si la criatura es nueva este mes
 */
const isNewThisMonth = (critter) => {
  const monthArray = critter.month_array_northern || []
  const prevMonth = currentMonth === 1 ? 12 : currentMonth - 1
  return monthArray.includes(currentMonth) && !monthArray.includes(prevMonth)
}

const availableNow = computed(() => allCritters.value.filter(isAvailableNow))
const leavingThisMonth = computed(() => allCritters.value.filter(isLeavingThisMonth))
const newArrivals = computed(() => allCritters.value.filter(isNewThisMonth))

/**
 * Filtra por tipo de criatura
 */
const filterByType = (critters) => {
  if (activeTypeFilter.value === 'all') return critters
  if (activeTypeFilter.value === 'fish') return critters.filter(c => c.type === 'fish')
  if (activeTypeFilter.value === 'bugs') return critters.filter(c => c.type === 'bug')
  if (activeTypeFilter.value === 'sea') return critters.filter(c => c.type === 'sea')
  return critters
}

/**
 * Lista final de criaturas filtradas
 */
const filteredCritters = computed(() => {
  let result = []
  switch (activeTimeTab.value) {
    case 'available':
      result = availableNow.value
      break
    case 'leaving':
      result = leavingThisMonth.value
      break
    case 'new':
      result = newArrivals.value
      break
  }
  return filterByType(result)
})

const getCountForTab = (tabKey) => {
  switch (tabKey) {
    case 'available': return availableNow.value.length
    case 'leaving': return leavingThisMonth.value.length
    case 'new': return newArrivals.value.length
    default: return 0
  }
}

const emptyMessage = computed(() => {
  switch (activeTimeTab.value) {
    case 'available':
      return 'No hay criaturas disponibles en este momento.'
    case 'leaving':
      return '¡Genial! Ninguna criatura se va este mes.'
    case 'new':
      return 'No hay criaturas nuevas este mes.'
    default:
      return 'No se encontraron criaturas.'
  }
})
</script>
