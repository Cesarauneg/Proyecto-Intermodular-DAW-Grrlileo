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
const drawerOpen = ref(false)

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

const selectFossil = (fossil) => {
  selectedFossil.value = fossil
  drawerOpen.value = false
}
const openDrawer = () => { drawerOpen.value = true }
const closeDrawer = () => { drawerOpen.value = false }

const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'fossils')
}
</script>

<template>
  <article
    class="relative flex flex-col lg:flex-row h-full bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl overflow-hidden"
    aria-label="Catálogo de fósiles"
  >
    <!-- Lista: visible en desktop siempre, en móvil solo cuando no hay selección -->
    <CritterList
      :class="selectedFossil ? 'hidden lg:flex' : 'flex'"
      title="Todos los fósiles"
      :items="filteredFossils"
      :selected-item="selectedFossil"
      :available-ids="new Set()"
      @select="selectFossil"
    />

    <!-- Drawer móvil (dentro del contenedor) -->
    <Transition name="drawer">
      <div v-if="drawerOpen" class="lg:hidden absolute inset-0 z-20 flex">
        <div class="absolute inset-0 bg-black/50" @click="closeDrawer"></div>
        <div class="relative w-4/5 max-w-xs h-full bg-white shadow-2xl overflow-hidden">
          <CritterList
            title="Todos los fósiles"
            :items="filteredFossils"
            :selected-item="selectedFossil"
            :available-ids="new Set()"
            class="h-full"
            @select="selectFossil"
          />
        </div>
      </div>
    </Transition>

    <!-- Detalle -->
    <main :class="[
      'flex-1 flex items-center justify-center p-4',
      selectedFossil ? 'flex' : 'hidden lg:flex'
    ]">
      <CritterDetail
        v-if="selectedFossil"
        :critter="selectedFossil"
        :is-available="false"
        :is-in-museum="isInMuseum(selectedFossil.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        :show-availability="false"
        @toggle-museum="handleToggle(selectedFossil.id)"
        @back="openDrawer"
      />
      <EmptyState v-else icon="🦴" title="Selecciona un fósil" />
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
