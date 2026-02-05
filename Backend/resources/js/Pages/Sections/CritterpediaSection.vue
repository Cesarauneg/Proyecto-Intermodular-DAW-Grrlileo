<template>
  <section class="w-full" aria-labelledby="critterpedia-title">
    <!-- Header -->
    <header class="ac-section-header">
      <h2 id="critterpedia-title" class="ac-section-title">
        Critterpedia
      </h2>
      <p class="ac-section-subtitle">
        Explora todas las criaturas y coleccionables del juego
      </p>
    </header>

    <!-- Sub-navegación (oculta si se controla desde header) -->
    <nav v-if="!props.activeSubSection" class="flex justify-center mb-6 px-4" role="tablist" aria-label="Categorías de Critterpedia">
      <div class="inline-flex flex-wrap justify-center gap-2 rounded-xl bg-ac-nav-cream p-2 shadow-[0_4px_0_theme(colors.ac.nav-brown-shadow)] border-3 border-ac-nav-brown">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          role="tab"
          :id="`critter-tab-${tab.key}`"
          :aria-selected="activeTab === tab.key"
          :aria-controls="`critter-panel-${tab.key}`"
          :tabindex="activeTab === tab.key ? 0 : -1"
          :class="tabButtonClasses(tab.key)"
          @click="handleTabChange(tab.key)"
          @keydown.left.prevent="navigateTabs(-1)"
          @keydown.right.prevent="navigateTabs(1)"
          @keydown.home.prevent="handleTabChange(tabs[0].key)"
          @keydown.end.prevent="handleTabChange(tabs[tabs.length - 1].key)"
        >
          <span class="mr-1" aria-hidden="true">{{ tab.icon }}</span>
          {{ tab.label }}
        </button>
      </div>
    </nav>

    <!-- Buscador y Filtros -->
    <div class="px-4 mb-4">
      <SearchBar
        v-model="searchQuery"
        :placeholder="searchPlaceholder"
      >
        <!-- Filtro: Disponible ahora -->
        <div v-if="currentTab.hasAvailability" class="flex flex-col">
          <label :for="`filter-available-${activeTab}`" class="sr-only">Filtrar por disponibilidad</label>
          <select
            :id="`filter-available-${activeTab}`"
            v-model="filters.available"
            :class="selectClasses"
          >
            <option value="">Disponibilidad</option>
            <option value="now">Disponible ahora</option>
            <option value="not">No disponible</option>
          </select>
        </div>

        <!-- Filtro: Ubicación -->
        <div v-if="showLocationFilter" class="flex flex-col">
          <label :for="`filter-location-${activeTab}`" class="sr-only">Filtrar por ubicación</label>
          <select
            :id="`filter-location-${activeTab}`"
            v-model="filters.location"
            :class="selectClasses"
          >
            <option value="">Ubicación</option>
            <option v-for="loc in currentLocations" :key="loc" :value="loc">{{ loc }}</option>
          </select>
        </div>

        <!-- Filtro: Rareza -->
        <div v-if="showRarityFilter" class="flex flex-col">
          <label :for="`filter-rarity-${activeTab}`" class="sr-only">Filtrar por rareza</label>
          <select
            :id="`filter-rarity-${activeTab}`"
            v-model="filters.rarity"
            :class="selectClasses"
          >
            <option value="">Rareza</option>
            <option value="Common">Común</option>
            <option value="Uncommon">Poco común</option>
            <option value="Rare">Raro</option>
            <option value="Ultra-rare">Ultra raro</option>
          </select>
        </div>

        <!-- Filtro: Precio -->
        <div v-if="showPriceFilter" class="flex flex-col">
          <label :for="`filter-price-${activeTab}`" class="sr-only">Filtrar por precio</label>
          <select
            :id="`filter-price-${activeTab}`"
            v-model="filters.price"
            :class="selectClasses"
          >
            <option value="">Precio</option>
            <option value="low">Bajo (&lt; 1,000)</option>
            <option value="medium">Medio (1,000 - 5,000)</option>
            <option value="high">Alto (5,000 - 10,000)</option>
            <option value="premium">Premium (&gt; 10,000)</option>
          </select>
        </div>

        <!-- Filtro: Velocidad -->
        <div v-if="showSpeedFilter" class="flex flex-col">
          <label :for="`filter-speed-${activeTab}`" class="sr-only">Filtrar por velocidad</label>
          <select
            :id="`filter-speed-${activeTab}`"
            v-model="filters.speed"
            :class="selectClasses"
          >
            <option value="">Velocidad</option>
            <option value="Stationary">Inmóvil</option>
            <option value="Very slow">Muy lento</option>
            <option value="Slow">Lento</option>
            <option value="Medium">Medio</option>
            <option value="Fast">Rápido</option>
            <option value="Very fast">Muy rápido</option>
          </select>
        </div>

        <!-- Filtro: En museo -->
        <div v-if="isAuthenticated" class="flex flex-col">
          <label :for="`filter-museum-${activeTab}`" class="sr-only">Filtrar por museo</label>
          <select
            :id="`filter-museum-${activeTab}`"
            v-model="filters.museum"
            :class="selectClasses"
          >
            <option value="">Museo</option>
            <option value="donated">Donado</option>
            <option value="missing">Sin donar</option>
          </select>
        </div>

        <!-- Filtro: Falsificación -->
        <div v-if="showFakeFilter" class="flex flex-col">
          <label :for="`filter-fake-${activeTab}`" class="sr-only">Filtrar por falsificación</label>
          <select
            :id="`filter-fake-${activeTab}`"
            v-model="filters.hasFake"
            :class="selectClasses"
          >
            <option value="">Autenticidad</option>
            <option value="yes">Tiene falsificación</option>
            <option value="no">Sin falsificación</option>
          </select>
        </div>
      </SearchBar>
    </div>

    <!-- Panel de contenido -->
    <div
      :id="`critter-panel-${activeTab}`"
      role="tabpanel"
      :aria-labelledby="`critter-tab-${activeTab}`"
      class="critterpedia-content"
    >
      <KeepAlive>
        <component
          :is="currentComponent"
          :key="activeTab"
          :search-query="searchQuery"
          :filter-available="filters.available"
          :filter-museum="filters.museum"
          :filter-has-fake="filters.hasFake"
          :filter-location="filters.location"
          :filter-rarity="filters.rarity"
          :filter-price="filters.price"
          :filter-speed="filters.speed"
          @update:bug-locations="bugLocations = $event"
          @update:fish-locations="fishLocations = $event"
        />
      </KeepAlive>
    </div>
  </section>
</template>

<script setup>
/**
 * @fileoverview Sección Critterpedia con navegación interna, buscador y filtros.
 * Integra las páginas de bichos, peces, criaturas marinas, fósiles y arte.
 *
 * @description Componente contenedor que gestiona:
 * - Navegación por tabs entre diferentes categorías
 * - Buscador global con filtros contextuales
 * - Estado de filtros que se resetean al cambiar de tab
 */

import { ref, reactive, computed, markRaw, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SearchBar from '@/Components/SearchBar.vue'
import BugPedia from '@/Pages/BugPedia.vue'
import FishPedia from '@/Pages/FishPedia.vue'
import SeaCreaturePedia from '@/Pages/SeaCreaturePedia.vue'
import FossilPedia from '@/Pages/FossilPedia.vue'
import ArtPedia from '@/Pages/ArtPedia.vue'

// =============================================
// PROPS
// =============================================
const props = defineProps({
  activeSubSection: {
    type: String,
    default: null
  }
})

// =============================================
// AUTH
// =============================================
const page = usePage()
const isAuthenticated = computed(() => !!page.props.auth?.user)

// =============================================
// CONFIGURACIÓN DE TABS
// =============================================

/**
 * @typedef {Object} Tab
 * @property {string} key - Identificador único del tab
 * @property {string} label - Texto visible del tab
 * @property {string} icon - Emoji del tab
 * @property {Component} component - Componente Vue a renderizar
 * @property {string} placeholder - Placeholder del buscador
 * @property {boolean} hasAvailability - Si muestra filtro de disponibilidad
 */
const tabs = [
  { key: 'bichos', label: 'Bichos', icon: '🦋', component: markRaw(BugPedia), placeholder: 'Buscar bicho...', hasAvailability: true },
  { key: 'peces', label: 'Peces', icon: '🐟', component: markRaw(FishPedia), placeholder: 'Buscar pez...', hasAvailability: true },
  { key: 'marinos', label: 'Criaturas marinas', icon: '🦑', component: markRaw(SeaCreaturePedia), placeholder: 'Buscar criatura marina...', hasAvailability: true },
  { key: 'fosiles', label: 'Fósiles', icon: '🦴', component: markRaw(FossilPedia), placeholder: 'Buscar fósil...', hasAvailability: false },
  { key: 'arte', label: 'Arte', icon: '🎨', component: markRaw(ArtPedia), placeholder: 'Buscar obra de arte...', hasAvailability: false }
]

// =============================================
// ESTADO REACTIVO
// =============================================
const activeTab = ref(props.activeSubSection || 'bichos')
const searchQuery = ref('')
const bugLocations = ref([])
const fishLocations = ref([])

const filters = reactive({
  available: '',
  museum: '',
  hasFake: '',
  location: '',
  rarity: '',
  price: '',
  speed: ''
})

// Sincronizar con prop externa
watch(() => props.activeSubSection, (newVal) => {
  if (newVal && tabs.some(t => t.key === newVal)) {
    activeTab.value = newVal
    searchQuery.value = ''
    Object.keys(filters).forEach(key => filters[key] = '')
  }
})

// =============================================
// COMPUTED PROPERTIES
// =============================================

/** Tab actualmente seleccionado */
const currentTab = computed(() => tabs.find(tab => tab.key === activeTab.value) || tabs[0])

/** Componente a renderizar según tab activo */
const currentComponent = computed(() => currentTab.value.component)

/** Placeholder del buscador según tab activo */
const searchPlaceholder = computed(() => currentTab.value.placeholder)

/** Ubicaciones disponibles según tab activo */
const currentLocations = computed(() => {
  if (activeTab.value === 'bichos') return bugLocations.value
  if (activeTab.value === 'peces') return fishLocations.value
  return []
})

// Visibilidad de filtros
const showLocationFilter = computed(() => ['bichos', 'peces'].includes(activeTab.value))
const showRarityFilter = computed(() => ['bichos', 'peces'].includes(activeTab.value))
const showPriceFilter = computed(() => ['bichos', 'peces', 'marinos'].includes(activeTab.value))
const showSpeedFilter = computed(() => activeTab.value === 'marinos')
const showFakeFilter = computed(() => activeTab.value === 'arte')

// =============================================
// CLASES CSS
// =============================================

/** Clases comunes para todos los selects */
const selectClasses = 'text-xs rounded-lg border-gray-200 focus:ring-2 focus:ring-green-400 focus:border-transparent'

/**
 * Genera clases para botones de tab
 * @param {string} tabKey - Clave del tab
 * @returns {string[]} Array de clases
 */
const tabButtonClasses = (tabKey) => [
  'px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
  'focus:outline-none focus:ring-2 focus:ring-ac-nav-brown focus:ring-offset-2',
  activeTab.value === tabKey
    ? 'bg-ac-nav-brown text-white shadow-md'
    : 'text-ac-nav-brown hover:bg-ac-nav-brown/10'
]

// =============================================
// MÉTODOS
// =============================================

/**
 * Cambia de tab y resetea búsqueda y filtros
 * @param {string} tabKey - Clave del nuevo tab
 */
const handleTabChange = (tabKey) => {
  activeTab.value = tabKey
  searchQuery.value = ''
  Object.keys(filters).forEach(key => filters[key] = '')
}

/**
 * Navega entre tabs con flechas del teclado
 * @param {number} direction - Dirección: -1 (izquierda) o 1 (derecha)
 */
const navigateTabs = (direction) => {
  const currentIndex = tabs.findIndex(t => t.key === activeTab.value)
  const newIndex = (currentIndex + direction + tabs.length) % tabs.length
  handleTabChange(tabs[newIndex].key)
}
</script>

<style scoped>
.critterpedia-content {
  height: 50vh;
  min-height: 400px;
  display: flex;
  flex-direction: column;
}

.critterpedia-content > * {
  flex: 1;
  height: 100%;
}
</style>
