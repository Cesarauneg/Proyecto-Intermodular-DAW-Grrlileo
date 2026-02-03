<script setup>
/**
 * @fileoverview Página de catálogo de arte.
 * Muestra lista filtrable de obras de arte con detalles y gestión de museo.
 *
 * @description Los datos se obtienen de:
 * - /api/art - Lista completa de obras de arte
 * - /user/art - Estado del museo del usuario (useMuseum)
 */

import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

/**
 * @typedef {Object} Art
 * @property {number} id - ID único
 * @property {string} name_es - Nombre en español
 * @property {string} name - Nombre en inglés
 * @property {boolean} has_fake - Si tiene versión falsificada
 * @property {number} buy_price - Precio de compra
 * @property {number} sell_price - Precio de venta
 */

const props = defineProps({
  searchQuery: { type: String, default: '' },
  filterMuseum: { type: String, default: '' },
  filterHasFake: { type: String, default: '' }
})

// ESTADO Y MUSEO
const { isInMuseum, toggleDonation } = useMuseum('/user/art')
const museumIconUrl = '/icons/museum.png'

const selectedArt = ref(null)
const drawerOpen = ref(false)

// AUTH
const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user !== null)

// FETCH DE DATOS
const { data: arts } = useFetch('/api/art')

/**
 * Lista de obras filtrada según los criterios activos
 */
const filteredArts = computed(() => {
  if (!arts.value) return []

  let result = arts.value

  if (props.searchQuery) {
    const query = props.searchQuery.toLowerCase()
    result = result.filter(a =>
      a.name_es?.toLowerCase().includes(query) ||
      a.name?.toLowerCase().includes(query)
    )
  }

  if (props.filterMuseum === 'donated') {
    result = result.filter(a => isInMuseum(a.id))
  } else if (props.filterMuseum === 'missing') {
    result = result.filter(a => !isInMuseum(a.id))
  }

  if (props.filterHasFake === 'yes') {
    result = result.filter(a => a.has_fake === true)
  } else if (props.filterHasFake === 'no') {
    result = result.filter(a => a.has_fake === false)
  }

  return result
})

// Limpiar selección si el item filtrado ya no está visible
watch([() => props.searchQuery, () => props.filterMuseum, () => props.filterHasFake], () => {
  if (selectedArt.value && !filteredArts.value.find(a => a.id === selectedArt.value.id)) {
    selectedArt.value = null
  }
})

const selectArt = (art) => {
  selectedArt.value = art
  drawerOpen.value = false
}
const openDrawer = () => { drawerOpen.value = true }
const closeDrawer = () => { drawerOpen.value = false }

const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'art')
}
</script>

<template>
  <article
    class="relative flex flex-col lg:flex-row h-full bg-gradient-to-br from-red-50 to-purple-50 rounded-xl overflow-hidden"
    aria-label="Catálogo de arte"
  >
    <!-- Lista: visible en desktop siempre, en móvil solo cuando no hay selección -->
    <CritterList
      :class="selectedArt ? 'hidden lg:flex' : 'flex'"
      title="Todas las obras"
      :items="filteredArts"
      :selected-item="selectedArt"
      :available-ids="new Set()"
      @select="selectArt"
    />

    <!-- Drawer móvil (dentro del contenedor) -->
    <Transition name="drawer">
      <div v-if="drawerOpen" class="lg:hidden absolute inset-0 z-20 flex">
        <div class="absolute inset-0 bg-black/50" @click="closeDrawer"></div>
        <div class="relative w-4/5 max-w-xs h-full bg-white shadow-2xl overflow-hidden">
          <CritterList
            title="Todas las obras"
            :items="filteredArts"
            :selected-item="selectedArt"
            :available-ids="new Set()"
            class="h-full"
            @select="selectArt"
          />
        </div>
      </div>
    </Transition>

    <!-- Detalle -->
    <main :class="[
      'flex-1 flex items-center justify-center p-4',
      selectedArt ? 'flex' : 'hidden lg:flex'
    ]">
      <CritterDetail
        v-if="selectedArt"
        :critter="selectedArt"
        :is-available="false"
        :is-in-museum="isInMuseum(selectedArt.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        :show-availability="false"
        @toggle-museum="handleToggle(selectedArt.id)"
        @back="openDrawer"
      />
      <EmptyState v-else icon="🎨" title="Selecciona una obra" />
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
