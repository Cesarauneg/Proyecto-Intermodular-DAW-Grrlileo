<script setup>
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3' 
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

// --- ESTADO Y MUSEO ---
// Importante: El endpoint coincide con lo que espera tu API
const { isInMuseum, toggleDonation } = useMuseum('/user/sea_creatures')
const museumIconUrl = '/icons/museum.png'

const selectedCreature = ref(null)
const availableCreaturesIds = ref(new Set())

// --- AUTH ---
const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user !== null)

// --- FETCH DE DATOS ---
const { data: seaCreatures } = useFetch('/api/sea_creatures')
const { data: availableData } = useFetch('/api/sea_creatures/available?hemisphere=north')

// --- WATCHERS ---
watch(availableData, (newData) => {
  if (newData) availableCreaturesIds.value = new Set(newData.map(item => item.id))
}, { immediate: true })

// --- MÉTODOS ---
const selectCreature = (creature) => { 
  selectedCreature.value = creature 
}

const isAvailable = computed(() => {
  return selectedCreature.value ? availableCreaturesIds.value.has(selectedCreature.value.id) : false
})

const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'sea_creatures') 
}
</script>

<template>
  <div class="flex flex-col lg:flex-row h-screen bg-gradient-to-br from-indigo-50 to-blue-100">
    
    <CritterList
      title="Criaturas Marinas"
      icon="🤿"
      :items="seaCreatures || []"
      :selected-item="selectedCreature"
      :available-ids="availableCreaturesIds"
      @select="selectCreature"
    />

    <div class="flex-1 flex items-center justify-center p-4 overflow-y-auto">
      <CritterDetail
        v-if="selectedCreature"
        :critter="selectedCreature"
        :is-available="isAvailable"
        :is-in-museum="isInMuseum(selectedCreature.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        @toggle-museum="handleToggle(selectedCreature.id)"
      />
      <EmptyState v-else icon="🪸" title="Selecciona una criatura marina" />
    </div>
  </div>
</template>