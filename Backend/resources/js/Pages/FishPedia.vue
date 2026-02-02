<template>
  <div class="flex flex-col lg:flex-row h-screen bg-gradient-to-br from-blue-50 to-cyan-50">

    <!-- Lista de peces -->
    <CritterList
      title="Todos los peces"
      :items="fish || []"
      :selected-item="selectedFish"
      :available-ids="availableFishIds"
      @select="selectFish"
    />

    <!-- Detalle -->
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">

      <CritterDetail
        v-if="selectedFish"
        :critter="selectedFish"
        :is-available="fishAvailability"
        :is-in-museum="isInMuseum(selectedFish.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        @toggle-museum="toggleMuseum(selectedFish.id)"
      />

      <EmptyState
        v-else
        icon="🐟"
        title="Selecciona un pez"
        subtitle="para ver su información"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useFetch } from '@/Composables/useFetch.js'
import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

const selectedFish = ref(null)
const availableFishIds = ref(new Set())
const museumFishIds = ref(new Set())

const museumIconUrl = '/icons/museum.png'
const isAuthenticated = ref(false)

// API
const { data: fish } = useFetch('/api/fish')
const { data: availableFishData } = useFetch('/api/fish/available?hemisphere=north')

// Disponibles
watch(availableFishData, (newData) => {
  if (Array.isArray(newData)) {
    availableFishIds.value = new Set(newData.map(f => f.id))
  }
}, { immediate: true })

watch(fish, (data) => {
  console.log('🐟 DATOS DE LA API:', data?.[0])
}, { immediate: true })


// Selección
const selectFish = (fish) => {
  selectedFish.value = fish
}

// Disponible?
const fishAvailability = computed(() => {
  if (!selectedFish.value) return false
  return availableFishIds.value.has(selectedFish.value.id)
})

// Museo
const isInMuseum = (id) => {
  return museumFishIds.value.has(id)
}

const toggleMuseum = (id) => {
  if (museumFishIds.value.has(id)) {
    museumFishIds.value.delete(id)
  } else {
    museumFishIds.value.add(id)
  }

  museumFishIds.value = new Set(museumFishIds.value)
}
</script>
