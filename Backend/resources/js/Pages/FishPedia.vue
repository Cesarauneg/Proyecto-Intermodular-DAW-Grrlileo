<!--
  @fileoverview Página de catálogo de peces.
  Usa el composable useCritterpedia y el layout CritterpediaLayout.
-->
<script setup>
import { watch, computed } from 'vue'
import { useCritterpedia } from '@/Composables/useCritterpedia.js'
import CritterpediaLayout from '@/Components/Critterpedia/CritterpediaLayout.vue'
import CritterDetail from '@/Components/CritterDetail.vue'

const props = defineProps({
  searchQuery: { type: String, default: '' },
  filterAvailable: { type: String, default: '' },
  filterMuseum: { type: String, default: '' },
  filterLocation: { type: String, default: '' },
  filterRarity: { type: String, default: '' },
  filterPrice: { type: String, default: '' }
})

const emit = defineEmits(['update:fish-locations'])

// Usar composable con configuración específica de peces
const {
  items: fish,
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
  apiEndpoint: '/api/fish',
  availableEndpoint: '/api/fish/available?hemisphere=north',
  museumEndpoint: '/user/fish',
  museumType: 'fish'
})

// Filtrado específico
const filteredFish = createFilteredItems(props)

// Limpiar selección cuando cambian los filtros
const filterProps = computed(() => [
  props.searchQuery,
  props.filterAvailable,
  props.filterMuseum,
  props.filterLocation,
  props.filterRarity,
  props.filterPrice
])
createSelectionWatcher(filteredFish, filterProps)

// Emitir ubicaciones únicas
watch(fish, (data) => {
  if (data) {
    const locations = [...new Set(data.map(f => f.location).filter(Boolean))].sort()
    emit('update:fish-locations', locations)
  }
}, { immediate: true })
</script>

<template>
  <CritterpediaLayout
    list-title="Todos los peces"
    :filtered-items="filteredFish"
    :selected-item="selectedItem"
    :available-ids="availableIds"
    :drawer-open="drawerOpen"
    background-class="from-blue-50 to-cyan-50"
    aria-label="Catálogo de peces"
    empty-icon="🐟"
    empty-title="Selecciona un pez"
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
        item-type="peces"
        @toggle-museum="handleToggle(selectedItem.id)"
        @back="openDrawer"
      />
    </template>
  </CritterpediaLayout>
</template>
