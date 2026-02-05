<!--
  @fileoverview Página de catálogo de bichos.
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

const emit = defineEmits(['update:bug-locations'])

// Usar composable con configuración específica de bichos
const {
  items: bugs,
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
  apiEndpoint: 'api/bugs',
  availableEndpoint: 'api/bugs/available?hemisphere=north',
  museumType: 'bugs'
})

// Filtrado específico
const filteredBugs = createFilteredItems(props)

// Limpiar selección cuando cambian los filtros
const filterProps = computed(() => [
  props.searchQuery,
  props.filterAvailable,
  props.filterMuseum,
  props.filterLocation,
  props.filterRarity,
  props.filterPrice
])
createSelectionWatcher(filteredBugs, filterProps)

// Emitir ubicaciones únicas
watch(bugs, (data) => {
  if (Array.isArray(data)) {
    const locations = [...new Set(data.map(b => b.location).filter(Boolean))].sort()
    emit('update:bug-locations', locations)
  }
}, { immediate: true })
</script>

<template>
  <CritterpediaLayout
    list-title="Todos los bichos"
    :filtered-items="filteredBugs"
    :selected-item="selectedItem"
    :available-ids="availableIds"
    :drawer-open="drawerOpen"
    background-class="from-green-50 to-blue-50"
    aria-label="Catálogo de bichos"
    empty-icon="🦋"
    empty-title="Selecciona un bicho"
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
        item-type="bichos"
        @toggle-museum="handleToggle(selectedItem.id)"
        @back="openDrawer"
      />
    </template>
  </CritterpediaLayout>
</template>

