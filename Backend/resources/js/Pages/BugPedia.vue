<template>
  <div class="flex flex-col lg:flex-row h-screen bg-gradient-to-br from-green-50 to-blue-50">
    
    <!-- Lista de bichos (30% en desktop) -->
    <CritterList
      title="Todos los bichos"
      icon=""
      :items="bugs || []"
      :selected-item="selectedBug"
      :available-ids="availableBugsIds"
      @select="selectBug"
    />

    <!-- Detalles del bicho (70% en desktop) -->
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">
      
      <!-- Detalles cuando hay un bicho seleccionado -->
      <CritterDetail
        v-if="selectedBug"
        :critter="selectedBug"
        :is-available="bugAvailability"
        :is-in-museum="isInMuseum(selectedBug.id)"
        :museum-icon-url="museumIconUrl"
        :show-museum-button="isAuthenticated"
        @toggle-museum="toggleMuseum(selectedBug.id)"
      />

      <!-- Estado vacío cuando no hay nada seleccionado -->
      <EmptyState
        v-else
        icon="🦋"
        title="Selecciona un bicho"
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

const selectedBug = ref(null)
const availableBugsIds = ref(new Set())
const museumBugsIds = ref(new Set()) // IDs de los bichos en el museo del usuario

// URL del icono del museo
const museumIconUrl = '/icons/museum.png'

// TODO: Cuando implementen la autenticación, reemplazar esto con la lógica real
// Ejemplo con Inertia: const user = usePage().props.auth.user
// Ejemplo con Pinia: const authStore = useAuthStore()
const isAuthenticated = ref(false) // Cambiar a true para probar el botón del museo

// Traemos los bichos
const { data: bugs, loading, error } = useFetch('/api/bugs')

// Traemos los bichos disponibles
const { data: availableBugsData } = useFetch('/api/bugs/available?hemisphere=north')

// Vigilar cuando se carguen los bichos disponibles
watch(availableBugsData, (newData) => {
  if (newData && Array.isArray(newData)) {
    availableBugsIds.value = new Set(newData.map(bug => bug.id))
  }
}, { immediate: true })

// TODO: Cuando tu compañera implemente el endpoint del museo, descomentar esto
// const { data: museumBugsData } = useFetch('/api/user/museum/bugs')
// watch(museumBugsData, (newData) => {
//   if (newData && Array.isArray(newData)) {
//     museumBugsIds.value = new Set(newData.map(bug => bug.id))
//   }
// }, { immediate: true })

// Función para seleccionar un bicho
const selectBug = (bug) => {
  selectedBug.value = bug
}

// Computed para saber si el bicho seleccionado está disponible
const bugAvailability = computed(() => {
  if (!selectedBug.value) return false
  return availableBugsIds.value.has(selectedBug.value.id)
})

// Función para verificar si un bicho está en el museo
const isInMuseum = (bugId) => {
  return museumBugsIds.value.has(bugId)
}

// Función para agregar/quitar del museo
const toggleMuseum = (bugId) => {
  // TODO: Cuando tu compañera implemente el endpoint, reemplazar esto
  // if (isInMuseum(bugId)) {
  //   await axios.delete(`/api/user/museum/bugs/${bugId}`)
  //   museumBugsIds.value.delete(bugId)
  // } else {
  //   await axios.post(`/api/user/museum/bugs/${bugId}`)
  //   museumBugsIds.value.add(bugId)
  // }
  
  // Por ahora, solo cambia el estado local (para que puedas probarlo)
  if (museumBugsIds.value.has(bugId)) {
    museumBugsIds.value.delete(bugId)
  } else {
    museumBugsIds.value.add(bugId)
  }
  
  // Forzar actualización reactiva
  museumBugsIds.value = new Set(museumBugsIds.value)
  
  console.log('Toggle museo para bicho:', bugId, 'En museo:', museumBugsIds.value.has(bugId))
}
</script>