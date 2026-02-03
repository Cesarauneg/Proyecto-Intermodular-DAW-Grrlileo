<template>
  <div class="flex flex-col lg:flex-row h-screen bg-gradient-to-br from-red-50 to-purple-50">
    
    <CritterList
      title="Todas las obras"
      :items="arts || []"
      :selected-item="selectedArt"
      :available-ids="new Set()"
      @select="selectArt"
    />

    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">
      
      <CritterDetail
          v-if="selectedArt"
          :critter="selectedArt"
          :is-available="false"
          :is-in-museum="isInMuseum(selectedArt.id)"
          :museum-icon-url="museumIconUrl"
          :show-museum-button="isAuthenticated"
          :show-availability="false"
          @toggle-museum="handleToggle(selectedArt.id)"
        />

      <EmptyState
        v-else
        icon="🎨"
        title="Selecciona una obra"
        subtitle="para ver su información"
      />

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue' 
import { usePage } from '@inertiajs/vue3' 
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

// ESTADO Y MUSEO
const { isInMuseum, toggleDonation } = useMuseum('/user/art')
const museumIconUrl = '/icons/museum.png'

const selectedArt = ref(null)

// AUTH 
const page = usePage()
const isAuthenticated = computed(() => page.props.auth?.user !== null)

// FETCH DE DATOS 
const { data: arts } = useFetch('/api/art')

// MÉTODOS 
const selectArt = (art) => {
  selectedArt.value = art
}

const handleToggle = (id) => {
  if (!isAuthenticated.value) return
  toggleDonation(id, 'art') 
}
</script>