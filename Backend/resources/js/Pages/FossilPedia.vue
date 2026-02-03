<!--
  @fileoverview Página de catálogo de fósiles.
  Usa el composable useCritterpedia y el layout CritterpediaLayout.
  Nota: Los fósiles no tienen disponibilidad temporal.
-->
<script setup>
import { computed } from 'vue'
import { useCritterpedia } from '@/Composables/useCritterpedia.js'
import CritterpediaLayout from '@/Components/Critterpedia/CritterpediaLayout.vue'
import CritterDetail from '@/Components/CritterDetail.vue'

const props = defineProps({
  searchQuery: { type: String, default: '' },
  filterMuseum: { type: String, default: '' }
})

// Usar composable con configuración específica de fósiles
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
  apiEndpoint: '/api/fossils',
  availableEndpoint: null, // Los fósiles no tienen disponibilidad
  museumEndpoint: '/user/fossils',
  museumType: 'fossils'
})

// Filtrado específico (solo búsqueda y museo)
const filteredFossils = createFilteredItems(props)

// Limpiar selección cuando cambian los filtros
const filterProps = computed(() => [
  props.searchQuery,
  props.filterMuseum
])
createSelectionWatcher(filteredFossils, filterProps)
</script>

<template>
  <CritterpediaLayout
    list-title="Todos los fósiles"
    :filtered-items="filteredFossils"
    :selected-item="selectedItem"
    :available-ids="availableIds"
    :drawer-open="drawerOpen"
    background-class="from-amber-50 to-yellow-50"
    aria-label="Catálogo de fósiles"
    empty-icon="🦴"
    empty-title="Selecciona un fósil"
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
        item-type="fósiles"
        @toggle-museum="handleToggle(selectedItem.id)"
        @back="openDrawer"
      />
    </template>
  </CritterpediaLayout>
</template>
