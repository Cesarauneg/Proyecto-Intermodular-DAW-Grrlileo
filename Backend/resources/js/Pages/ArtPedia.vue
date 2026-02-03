<!--
  @fileoverview Página de catálogo de arte.
  Usa el composable useCritterpedia y el layout CritterpediaLayout.
  Nota: El arte no tiene disponibilidad temporal.
-->
<script setup>
import { computed } from 'vue'
import { useCritterpedia } from '@/Composables/useCritterpedia.js'
import CritterpediaLayout from '@/Components/Critterpedia/CritterpediaLayout.vue'
import CritterDetail from '@/Components/CritterDetail.vue'

const props = defineProps({
  searchQuery: { type: String, default: '' },
  filterMuseum: { type: String, default: '' },
  filterHasFake: { type: String, default: '' }
})

// Usar composable con configuración específica de arte
const {
  selectedItem,
  availableIds,
  drawerOpen,
  isAuthenticated,
  museumIconUrl,
  createFilteredItems,
  createSelectionWatcher,
  selectItem,
  openDrawer,
  closeDrawer,
  handleToggle,
  isInMuseum
} = useCritterpedia({
  apiEndpoint: '/api/art',
  availableEndpoint: null, // El arte no tiene disponibilidad
  museumEndpoint: '/user/art',
  museumType: 'art'
})

// Filtrado específico (búsqueda, museo y falsificación)
const filteredArts = createFilteredItems(props)

// Limpiar selección cuando cambian los filtros
const filterProps = computed(() => [
  props.searchQuery,
  props.filterMuseum,
  props.filterHasFake
])
createSelectionWatcher(filteredArts, filterProps)
</script>

<template>
  <CritterpediaLayout
    list-title="Todas las obras"
    :filtered-items="filteredArts"
    :selected-item="selectedItem"
    :available-ids="availableIds"
    :drawer-open="drawerOpen"
    background-class="from-red-50 to-purple-50"
    aria-label="Catálogo de arte"
    empty-icon="🎨"
    empty-title="Selecciona una obra"
    @select="selectItem"
    @close-drawer="closeDrawer"
  >
    <template #detail>
      <CritterDetail
        :critter="selectedItem"
        :is-available="false"
        :is-in-museum="isInMuseum(selectedItem.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        :show-availability="false"
        item-type="obras de arte"
        @toggle-museum="handleToggle(selectedItem.id)"
        @back="openDrawer"
      />
    </template>
  </CritterpediaLayout>
</template>
