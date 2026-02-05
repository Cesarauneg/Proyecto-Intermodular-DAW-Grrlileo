<template>
  <section class="w-full px-4 flex flex-col h-full" aria-labelledby="temporada-title">
    <!-- Header -->
    <header class="ac-section-header">
      <h2 id="temporada-title" class="ac-section-title">
        Temporada
      </h2>
      <p class="ac-section-subtitle">
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
          :id="`tab-${tab.key}`"
          :aria-selected="activeTimeTab === tab.key"
          :aria-controls="`panel-${tab.key}`"
          :tabindex="activeTimeTab === tab.key ? 0 : -1"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
            'focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2',
            timeTabClasses(tab.key)
          ]"
          @click="activeTimeTab = tab.key"
          @keydown.left.prevent="navigateTab(-1)"
          @keydown.right.prevent="navigateTab(1)"
        >
          <span class="mr-1" aria-hidden="true">{{ tab.icon }}</span>
          {{ tab.label }}
          <span
            v-if="getCountForTab(tab.key) > 0"
            :class="[
              'ml-2 px-2 py-0.5 rounded-full text-xs',
              tab.key === 'leaving' ? 'bg-red-100 text-red-600' : 'bg-green-200 text-green-700'
            ]"
            :aria-label="`${getCountForTab(tab.key)} criaturas`"
          >
            {{ getCountForTab(tab.key) }}
          </span>
        </button>
      </div>
    </nav>

    <!-- Botón móvil para filtros de tipo (oculto si se controla desde header) -->
    <div v-if="!props.activeSubSection" class="flex justify-center mb-4 lg:hidden">
      <BackButton
        :label="currentTypeFilter.label"
        :icon="currentTypeFilter.icon"
        variant="amber"
        aria-label="Abrir filtros de tipo"
        @click="isMobileFilterOpen = !isMobileFilterOpen"
      />
    </div>

    <!-- Filtros por tipo de criatura (ocultos si se controla desde header) -->
    <nav
      v-if="!props.activeSubSection"
      :class="[
        'justify-center mb-6 px-4',
        isMobileFilterOpen ? 'flex' : 'hidden lg:flex'
      ]"
      aria-label="Filtros por tipo de criatura"
    >
      <div class="inline-flex flex-wrap justify-center gap-2 rounded-xl bg-ac-nav-cream p-2 shadow-[0_4px_0_theme(colors.ac.nav-brown-shadow)] border-3 border-ac-nav-brown">
        <button
          v-for="filter in typeFilters"
          :key="filter.key"
          :aria-pressed="activeTypeFilter === filter.key"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
            'focus:outline-none focus:ring-2 focus:ring-ac-nav-brown focus:ring-offset-2',
            activeTypeFilter === filter.key
              ? 'bg-ac-nav-brown text-white shadow-md'
              : 'text-ac-nav-brown hover:bg-ac-nav-brown/10'
          ]"
          @click="selectTypeFilter(filter.key)"
        >
          <span class="mr-1" aria-hidden="true">{{ filter.icon }}</span>
          {{ filter.label }}
        </button>
      </div>
    </nav>

    <!-- Mensaje informativo para "Ultima Oportunidad" -->
    <aside
      v-if="activeTimeTab === 'leaving' && filteredCritters.length > 0"
      class="max-w-2xl mx-auto mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-xl text-center"
      role="alert"
    >
      <p class="text-red-700 font-medium">
        <span class="text-lg" aria-hidden="true">⚠️</span>
        Estas criaturas no estaran disponibles a partir del proximo mes. ¡Atrapalas antes de que se vayan!
      </p>
    </aside>

    <!-- Panel de contenido -->
    <div
      :id="`panel-${activeTimeTab}`"
      role="tabpanel"
      :aria-labelledby="`tab-${activeTimeTab}`"
      class="flex flex-col flex-1"
    >
      <!-- Grid de criaturas -->
      <ul
        v-if="filteredCritters.length > 0"
        class="grid gap-4 sm:gap-6 flex-1 overflow-y-auto py-4 px-2 sm:px-4 max-h-[calc(100vh_-_350px)] grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        role="list"
        aria-label="Lista de criaturas"
      >
        <li
          v-for="critter in filteredCritters"
          :key="`${critter.type}-${critter.id}`"
        >
          <CritterCard
            :critter="critter"
            :type="critter.type"
            :urgent="activeTimeTab === 'leaving'"
          />
        </li>
      </ul>

      <!-- Estado vacio -->
      <div
        v-else
        class="text-center py-16 overflow-y-auto px-4 max-h-[calc(100vh_-_350px)]"
        role="status"
        aria-live="polite"
      >
        <div class="text-6xl mb-4" aria-hidden="true">
          {{ activeTimeTab === 'leaving' ? '🎉' : '🔍' }}
        </div>
        <p class="text-gray-500 text-lg">
          {{ emptyMessage }}
        </p>
      </div>
    </div>

    <!-- Resumen de temporada -->
    <aside
      v-if="filteredCritters.length > 0"
      class="mt-8 p-4 bg-green-50 rounded-xl border border-green-200"
      aria-label="Resumen de temporada"
    >
      <h3 class="font-semibold text-green-800 mb-2">Resumen de {{ monthName }}</h3>
      <dl class="flex flex-wrap gap-4 text-sm text-green-700">
        <div class="flex items-center gap-1">
          <dt class="sr-only">Disponibles ahora:</dt>
          <dd><span aria-hidden="true">🟢</span> Disponibles ahora: {{ filteredAvailableNow.length }}</dd>
        </div>
        <div class="flex items-center gap-1">
          <dt class="sr-only">Se van este mes:</dt>
          <dd><span aria-hidden="true">🔴</span> Se van este mes: {{ filteredLeavingThisMonth.length }}</dd>
        </div>
        <div class="flex items-center gap-1">
          <dt class="sr-only">Recien llegados:</dt>
          <dd><span aria-hidden="true">🆕</span> Recien llegados: {{ filteredNewArrivals.length }}</dd>
        </div>
      </dl>
    </aside>
  </section>
</template>

<script setup>
/**
 * @fileoverview Seccion de Temporada para la Critterpedia.
 * Muestra criaturas filtradas por disponibilidad temporal.
 *
 * @description Este componente recibe datos de criaturas desde el controlador
 * Laravel y las muestra filtradas por disponibilidad mensual y horaria.
 */

import { ref, computed, watch } from 'vue'
import CritterCard from '@/Components/CritterCard.vue'
import BackButton from '@/Components/Base/BackButton.vue'

/**
 * @typedef {Object} Critter
 * @property {number} id - ID único de la criatura
 * @property {string} name_es - Nombre en español
 * @property {string} icon - URL del icono
 * @property {number} price - Precio en bayas
 * @property {string} location - Ubicación donde encontrarla
 * @property {number[]} month_array_northern - Meses disponibles (hemisferio norte)
 * @property {number[]} time_array - Horas disponibles
 * @property {boolean} is_all_day - Si está disponible todo el día
 */

/**
 * Props recibidas desde el controlador Laravel (WelcomeController)
 * @see App\Http\Controllers\WelcomeController::index()
 */
const props = defineProps({
  /** @type {Critter[]} Lista de peces desde Fish::all() */
  fish: {
    type: Array,
    default: () => []
  },
  /** @type {Critter[]} Lista de bichos desde Bug::all() */
  bugs: {
    type: Array,
    default: () => []
  },
  /** @type {Critter[]} Lista de criaturas marinas desde SeaCreature::all() */
  seaCreatures: {
    type: Array,
    default: () => []
  },
  /** Subsección seleccionada desde el header */
  activeSubSection: {
    type: String,
    default: null
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
const isMobileFilterOpen = ref(false)

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

const activeTypeFilter = ref(props.activeSubSection || 'all')

// Sincronizar con prop externa (filtro de tipo)
watch(() => props.activeSubSection, (newVal) => {
  if (newVal && typeFilters.some(f => f.key === newVal)) {
    activeTypeFilter.value = newVal
  }
})

// Filtro de tipo actual
const currentTypeFilter = computed(() =>
  typeFilters.find(f => f.key === activeTypeFilter.value) || typeFilters[0]
)

// Seleccionar filtro de tipo
const selectTypeFilter = (filterKey) => {
  activeTypeFilter.value = filterKey
  isMobileFilterOpen.value = false
}

/**
 * Navega entre tabs con las flechas del teclado
 * @param {number} direction - Dirección de navegación (-1 izquierda, 1 derecha)
 */
const navigateTab = (direction) => {
  const currentIndex = timeTabs.findIndex(tab => tab.key === activeTimeTab.value)
  const newIndex = (currentIndex + direction + timeTabs.length) % timeTabs.length
  activeTimeTab.value = timeTabs[newIndex].key
}

/**
 * Genera las clases CSS para los tabs de tiempo
 * @param {string} tabKey - Clave del tab
 * @returns {string} Clases de Tailwind
 */
const timeTabClasses = (tabKey) => {
  return activeTimeTab.value === tabKey
    ? 'bg-white text-green-700 shadow-md'
    : 'text-green-600 hover:text-green-800 hover:bg-green-50'
}

/**
 * Combina peces, bichos y criaturas marinas en un array unificado con tipo
 * @returns {Array<Critter & {type: string}>}
 */
const allCritters = computed(() => {
  const fishWithType = props.fish.map(f => ({ ...f, type: 'fish' }))
  const bugsWithType = props.bugs.map(b => ({ ...b, type: 'bug' }))
  const seaWithType = props.seaCreatures.map(s => ({ ...s, type: 'sea' }))
  return [...fishWithType, ...bugsWithType, ...seaWithType]
})

/**
 * Verifica si una criatura esta disponible en el mes y hora actual
 * @param {Critter} critter - Criatura a verificar
 * @returns {boolean}
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
 * Verifica si la criatura se va este mes (disponible ahora pero no el próximo mes)
 * @param {Critter} critter - Criatura a verificar
 * @returns {boolean}
 */
const isLeavingThisMonth = (critter) => {
  const monthArray = critter.month_array_northern || []
  const nextMonth = currentMonth === 12 ? 1 : currentMonth + 1
  return monthArray.includes(currentMonth) && !monthArray.includes(nextMonth)
}

/**
 * Verifica si la criatura es nueva este mes (no disponible el mes pasado)
 * @param {Critter} critter - Criatura a verificar
 * @returns {boolean}
 */
const isNewThisMonth = (critter) => {
  const monthArray = critter.month_array_northern || []
  const prevMonth = currentMonth === 1 ? 12 : currentMonth - 1
  return monthArray.includes(currentMonth) && !monthArray.includes(prevMonth)
}

/** Criaturas disponibles en el momento actual */
const availableNow = computed(() => allCritters.value.filter(isAvailableNow))

/** Criaturas que se van este mes */
const leavingThisMonth = computed(() => allCritters.value.filter(isLeavingThisMonth))

/** Criaturas nuevas este mes */
const newArrivals = computed(() => allCritters.value.filter(isNewThisMonth))

/** Listas filtradas por tipo para los contadores */
const filteredAvailableNow = computed(() => filterByType(availableNow.value))
const filteredLeavingThisMonth = computed(() => filterByType(leavingThisMonth.value))
const filteredNewArrivals = computed(() => filterByType(newArrivals.value))

/**
 * Filtra criaturas por tipo
 * @param {Critter[]} critters - Lista de criaturas
 * @returns {Critter[]}
 */
const filterByType = (critters) => {
  if (activeTypeFilter.value === 'all') return critters
  if (activeTypeFilter.value === 'fish') return critters.filter(c => c.type === 'fish')
  if (activeTypeFilter.value === 'bugs') return critters.filter(c => c.type === 'bug')
  if (activeTypeFilter.value === 'sea') return critters.filter(c => c.type === 'sea')
  return critters
}

/**
 * Lista final de criaturas filtradas por tiempo y tipo
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

/**
 * Obtiene el contador para cada tab
 * @param {string} tabKey - Clave del tab
 * @returns {number}
 */
const getCountForTab = (tabKey) => {
  switch (tabKey) {
    case 'available': return filteredAvailableNow.value.length
    case 'leaving': return filteredLeavingThisMonth.value.length
    case 'new': return filteredNewArrivals.value.length
    default: return 0
  }
}

/**
 * Mensaje a mostrar cuando no hay criaturas
 */
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
