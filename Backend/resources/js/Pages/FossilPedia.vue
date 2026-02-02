<template>
  <div class="flex flex-col lg:flex-row h-screen bg-gradient-to-br from-amber-50 to-yellow-50">
    
    <!-- Lista de fósiles (30% en desktop) -->
    <CritterList
      title="Todos los fósiles"
      icon="🦴"
      :items="fossils || []"
      :selected-item="selectedFossil"
      :available-ids="new Set()"
      @select="selectFossil"
    />

    <!-- Detalles del fósil (70% en desktop) -->
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">
      
      <!-- Detalles cuando hay un fósil seleccionado -->
      <CritterDetail
        v-if="selectedFossil"
        :critter="selectedFossil"
        :is-available="false"
        :is-in-museum="isInMuseum(selectedFossil.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        :show-availability="false"
        @toggle-museum="toggleMuseum(selectedFossil.id)"
      />

      <!-- Estado vacío cuando no hay nada seleccionado -->
      <EmptyState
        v-else
        icon="🦴"
        title="Selecciona un fósil"
        subtitle="para ver su información"
      />

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useFetch } from '@/Composables/useFetch.js'
import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

const selectedFossil = ref(null)
const museumFossilIds = ref(new Set())

const museumIconUrl = '/icons/museum.png'
const isAuthenticated = ref(false)

// Traer fósiles
const { data: fossils } = useFetch('/api/fossils')

// Selección
const selectFossil = (fossil) => {
  selectedFossil.value = fossil
}

// Museo
const isInMuseum = (id) => {
  return museumFossilIds.value.has(id)
}

const toggleMuseum = (id) => {
  if (museumFossilIds.value.has(id)) {
    museumFossilIds.value.delete(id)
  } else {
    museumFossilIds.value.add(id)
  }
  museumFossilIds.value = new Set(museumFossilIds.value)
}
</script>