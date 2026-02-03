<template>
  <div class="flex flex-col lg:flex-row h-screen bg-gradient-to-br from-blue-50 to-cyan-50">

    <CritterList
      title="Todos los peces"
      :items="fish || []"
      :selected-item="selectedFish"
      :available-ids="availableFishIds"
      @select="selectFish"
    />

    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">

      <CritterDetail
        v-if="selectedFish"
        :critter="selectedFish"
        :is-available="fishAvailability"
        :is-in-museum="isInMuseum(selectedFish.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        @toggle-museum="handleToggle(selectedFish.id)"
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
import { usePage } from '@inertiajs/vue3'
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

// ESTADO Y MUSEO
const { isInMuseum, toggleDonation } = useMuseum('/user/fish')
const museumIconUrl = '/icons/museum.png'

const selectedFish = ref(null)
const availableFishIds = ref(new Set())

// AUTH
const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user !== null)

// FETCH DE DATOS
const { data: fish } = useFetch('/api/fish')
const { data: availableFishData } = useFetch('/api/fish/available?hemisphere=north')

// WATCHERS 
watch(availableFishData, (newData) => {
  if (Array.isArray(newData)) {
    availableFishIds.value = new Set(newData.map(f => f.id))
  }
}, { immediate: true })

// MÉTODOS 
const selectFish = (fishItem) => {
  selectedFish.value = fishItem
}

const fishAvailability = computed(() => {
  return selectedFish.value ? availableFishIds.value.has(selectedFish.value.id) : false
})

const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'fish') 
}
</script>