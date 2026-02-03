<script setup>
/**
 * @fileoverview Página de catálogo de peces.
 * Muestra lista filtrable de peces con detalles y gestión de museo.
 *
 * @description Los datos se obtienen de:
 * - /api/fish - Lista completa de peces
 * - /api/fish/available - Peces disponibles actualmente
 * - /user/fish - Estado del museo del usuario (useMuseum)
 */

import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

/**
 * @typedef {Object} Fish
 * @property {number} id - ID único del pez
 * @property {string} name_es - Nombre en español
 * @property {string} name - Nombre en inglés
 * @property {string} location - Ubicación donde encontrarlo
 * @property {string} rarity - Nivel de rareza
 * @property {number} price - Precio de venta en bayas
 */

const props = defineProps({
  searchQuery: { type: String, default: '' },
  filterAvailable: { type: String, default: '' },
  filterMuseum: { type: String, default: '' },
  filterLocation: { type: String, default: '' },
  filterRarity: { type: String, default: '' },
  filterPrice: { type: String, default: '' }
})

const emit = defineEmits(['update:fish-locations'])

// ESTADO Y MUSEO
const { isInMuseum, toggleDonation } = useMuseum('/user/fish')
const museumIconUrl = '/icons/museum.png'

const selectedFish = ref(null)
const availableFishIds = ref(new Set())
const drawerOpen = ref(false)

// AUTH
const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user !== null)

// FETCH DE DATOS
const { data: fish } = useFetch('/api/fish')
const { data: availableFishData } = useFetch('/api/fish/available?hemisphere=north')

// WATCHERS
watch(availableFishData, (newData) => {
  if (Array.isArray(newData)) {
    availableFishIds.value = new Set(newData.map(f => f.id))
  }
}, { immediate: true })

// Emitir ubicaciones únicas cuando se cargan los datos
watch(fish, (newData) => {
  if (newData) {
    const locations = [...new Set(newData.map(f => f.location).filter(Boolean))].sort()
    emit('update:fish-locations', locations)
  }
}, { immediate: true })

/**
 * Lista de peces filtrada según los criterios activos
 */
const filteredFish = computed(() => {
  if (!fish.value) return []

  let result = fish.value

  if (props.searchQuery) {
    const query = props.searchQuery.toLowerCase()
    result = result.filter(f =>
      f.name_es?.toLowerCase().includes(query) ||
      f.name?.toLowerCase().includes(query)
    )
  }

  if (props.filterAvailable === 'now') {
    result = result.filter(f => availableFishIds.value.has(f.id))
  } else if (props.filterAvailable === 'not') {
    result = result.filter(f => !availableFishIds.value.has(f.id))
  }

  if (props.filterLocation) {
    result = result.filter(f => f.location === props.filterLocation)
  }

  if (props.filterRarity) {
    result = result.filter(f => f.rarity === props.filterRarity)
  }

  if (props.filterPrice) {
    result = result.filter(f => {
      const price = f.price || 0
      switch (props.filterPrice) {
        case 'low': return price < 1000
        case 'medium': return price >= 1000 && price < 5000
        case 'high': return price >= 5000 && price < 10000
        case 'premium': return price >= 10000
        default: return true
      }
    })
  }

  if (props.filterMuseum === 'donated') {
    result = result.filter(f => isInMuseum(f.id))
  } else if (props.filterMuseum === 'missing') {
    result = result.filter(f => !isInMuseum(f.id))
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
  if (selectedFish.value && !filteredFish.value.find(f => f.id === selectedFish.value.id)) {
    selectedFish.value = null
  }
})

const selectFish = (fishItem) => {
  selectedFish.value = fishItem
  drawerOpen.value = false
}
const openDrawer = () => { drawerOpen.value = true }
const closeDrawer = () => { drawerOpen.value = false }

const fishAvailability = computed(() => {
  return selectedFish.value ? availableFishIds.value.has(selectedFish.value.id) : false
})

const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'fish')
}
</script>

<template>
  <article
    class="relative flex flex-col lg:flex-row h-full bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl overflow-hidden"
    aria-label="Catálogo de peces"
  >
    <!-- Lista: visible en desktop siempre, en móvil solo cuando no hay selección -->
    <CritterList
      :class="selectedFish ? 'hidden lg:flex' : 'flex'"
      title="Todos los peces"
      :items="filteredFish"
      :selected-item="selectedFish"
      :available-ids="availableFishIds"
      @select="selectFish"
    />

    <!-- Drawer móvil (dentro del contenedor) -->
    <Transition name="drawer">
      <div v-if="drawerOpen" class="lg:hidden absolute inset-0 z-20 flex">
        <div class="absolute inset-0 bg-black/50" @click="closeDrawer"></div>
        <div class="relative w-4/5 max-w-xs h-full bg-white shadow-2xl overflow-hidden">
          <CritterList
            title="Todos los peces"
            :items="filteredFish"
            :selected-item="selectedFish"
            :available-ids="availableFishIds"
            class="h-full"
            @select="selectFish"
          />
        </div>
      </div>
    </Transition>

    <!-- Detalle -->
    <main :class="[
      'flex-1 flex items-center justify-center p-4',
      selectedFish ? 'flex' : 'hidden lg:flex'
    ]">
      <CritterDetail
        v-if="selectedFish"
        :critter="selectedFish"
        :is-available="fishAvailability"
        :is-in-museum="isInMuseum(selectedFish.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        @toggle-museum="handleToggle(selectedFish.id)"
        @back="openDrawer"
      />
      <EmptyState v-else icon="🐟" title="Selecciona un pez" />
    </main>
  </article>
</template>

<style scoped>
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
