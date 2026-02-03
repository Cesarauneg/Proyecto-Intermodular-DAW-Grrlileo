<script setup>
/**
 * @fileoverview Página de catálogo de fósiles.
 * Muestra lista filtrable de fósiles con detalles y gestión de museo.
 *
 * @description Los datos se obtienen de:
 * - /api/fossils - Lista completa de fósiles
 * - /user/fossils - Estado del museo del usuario (useMuseum)
 */

import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

/**
 * @typedef {Object} Fossil
 * @property {number} id - ID único
 * @property {string} name_es - Nombre en español
 * @property {string} name - Nombre en inglés
 * @property {string} part_of - Dinosaurio al que pertenece
 * @property {number} price - Precio de venta en bayas
 */

const props = defineProps({
  searchQuery: { type: String, default: '' },
  filterMuseum: { type: String, default: '' }
})

// ESTADO Y MUSEO
const { isInMuseum, toggleDonation } = useMuseum('/user/fossils')
const museumIconUrl = '/icons/museum.png'

const selectedFossil = ref(null)

// AUTH
const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user !== null)

// FETCH DE DATOS
const { data: fossils } = useFetch('/api/fossils')

/**
 * Lista de fósiles filtrada según los criterios activos
 */
const filteredFossils = computed(() => {
  if (!fossils.value) return []

  let result = fossils.value

  if (props.searchQuery) {
    const query = props.searchQuery.toLowerCase()
    result = result.filter(f =>
      f.name_es?.toLowerCase().includes(query) ||
      f.name?.toLowerCase().includes(query) ||
      f.part_of?.toLowerCase().includes(query)
    )
  }

  if (props.filterMuseum === 'donated') {
    result = result.filter(f => isInMuseum(f.id))
  } else if (props.filterMuseum === 'missing') {
    result = result.filter(f => !isInMuseum(f.id))
  }

  return result
})

// Limpiar selección si el item filtrado ya no está visible
watch([() => props.searchQuery, () => props.filterMuseum], () => {
  if (selectedFossil.value && !filteredFossils.value.find(f => f.id === selectedFossil.value.id)) {
    selectedFossil.value = null
  }
})

const selectFossil = (fossil) => { selectedFossil.value = fossil }

const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'fossils')
}
</script>

<template>
  <article
    class="flex flex-col lg:flex-row h-full bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl overflow-hidden"
    aria-label="Catálogo de fósiles"
  >
    <CritterList
      title="Todos los fósiles"
      :items="filteredFossils"
      :selected-item="selectedFossil"
      :available-ids="new Set()"
      @select="selectFossil"
    />

    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">
      <CritterDetail
        v-if="selectedFossil"
        :critter="selectedFossil"
        :is-available="false"
        :is-in-museum="isInMuseum(selectedFossil.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        :show-availability="false"
        @toggle-museum="handleToggle(selectedFossil.id)"
      />

      <EmptyState
        v-else
        icon="🦴"
        title="Selecciona un fósil"
        subtitle="para ver su información"
      />
    </main>
  </article>
</template>
