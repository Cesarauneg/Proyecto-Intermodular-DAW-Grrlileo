<!--
  @fileoverview Página de catálogo de criaturas marinas.
  Usa el composable useCritterpedia y el layout CritterpediaLayout.
-->
<script setup>
import { computed } from 'vue'
import { useCritterpedia } from '@/Composables/useCritterpedia.js'
import CritterpediaLayout from '@/Components/Critterpedia/CritterpediaLayout.vue'
import CritterDetail from '@/Components/CritterDetail.vue'

const props = defineProps({
  searchQuery: { type: String, default: '' },
  filterAvailable: { type: String, default: '' },
  filterMuseum: { type: String, default: '' },
  filterPrice: { type: String, default: '' },
  filterSpeed: { type: String, default: '' }
})

// Usar composable con configuración específica de criaturas marinas
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
  isInMuseum,
  itemAvailability
} = useCritterpedia({
  apiEndpoint: '/api/sea_creatures',
  availableEndpoint: '/api/sea_creatures/available?hemisphere=north',
  museumType: 'sea_creatures'
})

// Filtrado específico
const filteredCreatures = createFilteredItems(props)

// Limpiar selección cuando cambian los filtros
const filterProps = computed(() => [
  props.searchQuery,
  props.filterAvailable,
  props.filterMuseum,
  props.filterPrice,
  props.filterSpeed
])
createSelectionWatcher(filteredCreatures, filterProps)
</script>

<template>
  <CritterpediaLayout
    list-title="Criaturas Marinas"
    :filtered-items="filteredCreatures"
    :selected-item="selectedItem"
    :available-ids="availableIds"
    :drawer-open="drawerOpen"
    background-class="from-indigo-50 to-blue-100"
    aria-label="Catálogo de criaturas marinas"
    empty-icon="🦑"
    empty-title="Selecciona una criatura marina"
    @select="selectItem"
    @close-drawer="closeDrawer"
  >
    <template #detail>
      <CritterDetail
        :critter="selectedItem"
        :is-available="itemAvailability"
        :is-in-museum="isInMuseum(selectedItem.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        item-type="criaturas marinas"
        @toggle-museum="handleToggle(selectedItem.id)"
        @back="openDrawer"
      />
    </template>
  </CritterpediaLayout>
</template>
