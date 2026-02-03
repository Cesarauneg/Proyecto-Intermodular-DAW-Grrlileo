<script setup>
/**
 * @fileoverview Página de catálogo de bichos.
 * Muestra lista filtrable de bichos con detalles y gestión de museo.
 *
 * @description Los datos se obtienen de:
 * - /api/bugs - Lista completa de bichos
 * - /api/bugs/available - Bichos disponibles actualmente
 * - /user/bugs - Estado del museo del usuario (useMuseum)
 */

import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

/**
 * @typedef {Object} Bug
 * @property {number} id - ID único del bicho
 * @property {string} name_es - Nombre en español
 * @property {string} name - Nombre en inglés
 * @property {string} location - Ubicación donde encontrarlo
 * @property {string} rarity - Nivel de rareza (Common, Uncommon, Rare, Ultra-rare)
 * @property {number} price - Precio de venta en bayas
 * @property {string} icon - URL del icono
 * @property {string} image - URL de la imagen grande
 */

const props = defineProps({
  /** Término de búsqueda desde CritterpediaSection */
  searchQuery: { type: String, default: '' },
  /** Filtro de disponibilidad: 'now', 'not', '' */
  filterAvailable: { type: String, default: '' },
  /** Filtro de museo: 'donated', 'missing', '' */
  filterMuseum: { type: String, default: '' },
  /** Filtro de ubicación */
  filterLocation: { type: String, default: '' },
  /** Filtro de rareza */
  filterRarity: { type: String, default: '' },
  /** Filtro de precio: 'low', 'medium', 'high', 'premium' */
  filterPrice: { type: String, default: '' }
})

const emit = defineEmits(['update:bug-locations'])

// ESTADO Y MUSEO
const { isInMuseum, toggleDonation } = useMuseum('/user/bugs')
const museumIconUrl = '/icons/museum.png'

const selectedBug = ref(null)
const availableBugsIds = ref(new Set())
const drawerOpen = ref(false)

// AUTH
const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user !== null)

// FETCH DE DATOS
const { data: bugs } = useFetch('/api/bugs')
const { data: availableBugsData } = useFetch('/api/bugs/available?hemisphere=north')

// WATCHERS
watch(availableBugsData, (newData) => {
  if (newData) availableBugsIds.value = new Set(newData.map(bug => bug.id))
}, { immediate: true })

// Emitir ubicaciones únicas cuando se cargan los datos
watch(bugs, (newData) => {
  if (newData) {
    const locations = [...new Set(newData.map(b => b.location).filter(Boolean))].sort()
    emit('update:bug-locations', locations)
  }
}, { immediate: true })

/**
 * Lista de bichos filtrada según los criterios activos
 * @returns {Bug[]}
 */
const filteredBugs = computed(() => {
  if (!bugs.value) return []

  let result = bugs.value

  // Filtro por búsqueda
  if (props.searchQuery) {
    const query = props.searchQuery.toLowerCase()
    result = result.filter(bug =>
      bug.name_es?.toLowerCase().includes(query) ||
      bug.name?.toLowerCase().includes(query)
    )
  }

  // Filtro por disponibilidad
  if (props.filterAvailable === 'now') {
    result = result.filter(bug => availableBugsIds.value.has(bug.id))
  } else if (props.filterAvailable === 'not') {
    result = result.filter(bug => !availableBugsIds.value.has(bug.id))
  }

  // Filtro por ubicación
  if (props.filterLocation) {
    result = result.filter(bug => bug.location === props.filterLocation)
  }

  // Filtro por rareza
  if (props.filterRarity) {
    result = result.filter(bug => bug.rarity === props.filterRarity)
  }

  // Filtro por precio
  if (props.filterPrice) {
    result = result.filter(bug => {
      const price = bug.price || 0
      switch (props.filterPrice) {
        case 'low': return price < 1000
        case 'medium': return price >= 1000 && price < 5000
        case 'high': return price >= 5000 && price < 10000
        case 'premium': return price >= 10000
        default: return true
      }
    })
  }

  // Filtro por museo
  if (props.filterMuseum === 'donated') {
    result = result.filter(bug => isInMuseum(bug.id))
  } else if (props.filterMuseum === 'missing') {
    result = result.filter(bug => !isInMuseum(bug.id))
  }

  return result
})

// Limpiar selección si el item filtrado ya no está visible
watch([
  () => props.searchQuery,
  () => props.filterAvailable,
  () => props.filterMuseum,
  () => props.filterLocation,
  () => props.filterRarity,
  () => props.filterPrice
], () => {
  if (selectedBug.value && !filteredBugs.value.find(b => b.id === selectedBug.value.id)) {
    selectedBug.value = null
  }
})

// MÉTODOS
const selectBug = (bug) => {
  selectedBug.value = bug
  drawerOpen.value = false // Cerrar drawer al seleccionar
}
const openDrawer = () => { drawerOpen.value = true }
const closeDrawer = () => { drawerOpen.value = false }

/** Verifica si el bicho seleccionado está disponible ahora */
const bugAvailability = computed(() => {
  return selectedBug.value ? availableBugsIds.value.has(selectedBug.value.id) : false
})

/**
 * Maneja el toggle de donación al museo
 * @param {number} id - ID del bicho
 */
const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'bugs')
}
</script>

<template>
  <article
    class="relative flex flex-col lg:flex-row h-full bg-gradient-to-br from-green-50 to-blue-50 rounded-xl overflow-hidden"
    aria-label="Catálogo de bichos"
  >
    <!-- Lista: visible en desktop siempre, en móvil solo cuando no hay selección -->
    <CritterList
      :class="selectedBug ? 'hidden lg:flex' : 'flex'"
      title="Todos los bichos"
      :items="filteredBugs"
      :selected-item="selectedBug"
      :available-ids="availableBugsIds"
      @select="selectBug"
    />

    <!-- Drawer móvil: overlay + lista lateral (dentro del contenedor) -->
    <Transition name="drawer">
      <div
        v-if="drawerOpen"
        class="lg:hidden absolute inset-0 z-20 flex"
      >
        <!-- Backdrop -->
        <div
          class="absolute inset-0 bg-black/50"
          @click="closeDrawer"
        ></div>

        <!-- Panel del drawer -->
        <div class="relative w-4/5 max-w-xs h-full bg-white shadow-2xl overflow-hidden">
          <CritterList
            title="Todos los bichos"
            :items="filteredBugs"
            :selected-item="selectedBug"
            :available-ids="availableBugsIds"
            class="h-full"
            @select="selectBug"
          />
        </div>
      </div>
    </Transition>

    <!-- Detalle: en móvil ocupa todo cuando hay selección, en desktop parte derecha -->
    <main :class="[
      'flex-1 flex items-center justify-center p-4',
      selectedBug ? 'flex' : 'hidden lg:flex'
    ]">
      <CritterDetail
        v-if="selectedBug"
        :critter="selectedBug"
        :is-available="bugAvailability"
        :is-in-museum="isInMuseum(selectedBug.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        @toggle-museum="handleToggle(selectedBug.id)"
        @back="openDrawer"
      />
      <EmptyState v-else icon="🦋" title="Selecciona un bicho" />
    </main>

  </article>
</template>

<style scoped>
/* Transiciones del drawer */
.drawer-enter-active,
.drawer-leave-active {
  transition: opacity 0.3s ease;
}

.drawer-enter-active > div:last-child,
.drawer-leave-active > div:last-child {
  transition: transform 0.3s ease;
}

.drawer-enter-from,
.drawer-leave-to {
  opacity: 0;
}

.drawer-enter-from > div:last-child,
.drawer-leave-to > div:last-child {
  transform: translateX(-100%);
}
</style>
