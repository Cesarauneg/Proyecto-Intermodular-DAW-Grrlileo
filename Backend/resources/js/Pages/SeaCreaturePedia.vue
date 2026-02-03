<script setup>
/**
 * @fileoverview Página de catálogo de criaturas marinas.
 * Muestra lista filtrable de criaturas marinas con detalles y gestión de museo.
 *
 * @description Los datos se obtienen de:
 * - /api/sea_creatures - Lista completa de criaturas marinas
 * - /api/sea_creatures/available - Criaturas disponibles actualmente
 * - /user/sea_creatures - Estado del museo del usuario (useMuseum)
 */

import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

/**
 * @typedef {Object} SeaCreature
 * @property {number} id - ID único
 * @property {string} name_es - Nombre en español
 * @property {string} name - Nombre en inglés
 * @property {number} price - Precio de venta en bayas
 * @property {string} speed - Velocidad de movimiento
 */

const props = defineProps({
  searchQuery: { type: String, default: '' },
  filterAvailable: { type: String, default: '' },
  filterMuseum: { type: String, default: '' },
  filterPrice: { type: String, default: '' },
  filterSpeed: { type: String, default: '' }
})

// ESTADO Y MUSEO
const { isInMuseum, toggleDonation } = useMuseum('/user/sea_creatures')
const museumIconUrl = '/icons/museum.png'

const selectedCreature = ref(null)
const availableCreaturesIds = ref(new Set())

// AUTH
const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user !== null)

// FETCH DE DATOS
const { data: seaCreatures } = useFetch('/api/sea_creatures')
const { data: availableData } = useFetch('/api/sea_creatures/available?hemisphere=north')

// WATCHERS
watch(availableData, (newData) => {
  if (newData) availableCreaturesIds.value = new Set(newData.map(item => item.id))
}, { immediate: true })

/**
 * Lista de criaturas filtrada según los criterios activos
 */
const filteredCreatures = computed(() => {
  if (!seaCreatures.value) return []

  let result = seaCreatures.value

  if (props.searchQuery) {
    const query = props.searchQuery.toLowerCase()
    result = result.filter(c =>
      c.name_es?.toLowerCase().includes(query) ||
      c.name?.toLowerCase().includes(query)
    )
  }

  if (props.filterAvailable === 'now') {
    result = result.filter(c => availableCreaturesIds.value.has(c.id))
  } else if (props.filterAvailable === 'not') {
    result = result.filter(c => !availableCreaturesIds.value.has(c.id))
  }

  if (props.filterPrice) {
    result = result.filter(c => {
      const price = c.price || 0
      switch (props.filterPrice) {
        case 'low': return price < 1000
        case 'medium': return price >= 1000 && price < 5000
        case 'high': return price >= 5000 && price < 10000
        case 'premium': return price >= 10000
        default: return true
      }
    })
  }

  if (props.filterSpeed) {
    result = result.filter(c => c.speed === props.filterSpeed)
  }

  if (props.filterMuseum === 'donated') {
    result = result.filter(c => isInMuseum(c.id))
  } else if (props.filterMuseum === 'missing') {
    result = result.filter(c => !isInMuseum(c.id))
  }

  return result
})

// Limpiar selección si el item filtrado ya no está visible
watch([
  () => props.searchQuery,
  () => props.filterAvailable,
  () => props.filterMuseum,
  () => props.filterPrice,
  () => props.filterSpeed
], () => {
  if (selectedCreature.value && !filteredCreatures.value.find(c => c.id === selectedCreature.value.id)) {
    selectedCreature.value = null
  }
})

const selectCreature = (creature) => { selectedCreature.value = creature }

const isAvailable = computed(() => {
  return selectedCreature.value ? availableCreaturesIds.value.has(selectedCreature.value.id) : false
})

const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'sea_creatures')
}
</script>

<template>
  <article
    class="flex flex-col lg:flex-row h-full bg-gradient-to-br from-indigo-50 to-blue-100 rounded-xl overflow-hidden"
    aria-label="Catálogo de criaturas marinas"
  >
    <CritterList
      title="Criaturas Marinas"
      :items="filteredCreatures"
      :selected-item="selectedCreature"
      :available-ids="availableCreaturesIds"
      @select="selectCreature"
    />

    <main class="flex-1 flex items-center justify-center p-4 overflow-y-auto">
      <CritterDetail
        v-if="selectedCreature"
        :critter="selectedCreature"
        :is-available="isAvailable"
        :is-in-museum="isInMuseum(selectedCreature.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        @toggle-museum="handleToggle(selectedCreature.id)"
      />
      <EmptyState v-else icon="🪸" title="Selecciona una criatura marina" />
    </main>
  </article>
</template>
