<script setup>
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3' 
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

// ESTADO Y MUSEO 
const { isInMuseum, toggleDonation } = useMuseum('/user/bugs')
const museumIconUrl = '/icons/museum.png'

const selectedBug = ref(null)
const availableBugsIds = ref(new Set())

// AUTH 
const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user !== null)

// FETCH DE DATOS 
const { data: bugs } = useFetch('/api/sea_creatures')
const { data: availableBugsData } = useFetch('/api/sea_creatures/available?hemisphere=north')

// WATCHERS 
watch(availableBugsData, (newData) => {
  if (newData) availableBugsIds.value = new Set(newData.map(bug => bug.id))
}, { immediate: true })

// MÉTODOS 
const selectBug = (bug) => { selectedBug.value = bug }

const bugAvailability = computed(() => {
  return selectedBug.value ? availableBugsIds.value.has(selectedBug.value.id) : false
})

const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'bugs') 
}
</script>

<template>
  <div class="flex flex-col lg:flex-row h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <CritterList
      :items="bugs || []"
      :selected-item="selectedBug"
      :available-ids="availableBugsIds"
      @select="selectBug"
    />

    <div class="flex-1 flex items-center justify-center p-4 overflow-y-auto">
      <CritterDetail
        v-if="selectedBug"
        :critter="selectedBug"
        :is-available="bugAvailability"
        :is-in-museum="isInMuseum(selectedBug.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        @toggle-museum="handleToggle(selectedBug.id)"
      />
      <EmptyState v-else icon="🪸" title="Selecciona una criatura marina" />
    </div>
  </div>
</template>