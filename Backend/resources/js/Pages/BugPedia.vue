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
import { usePage } from '@inertiajs/vue3' 
import { useFetch } from '@/Composables/useFetch.js'
import axios from 'axios'
import CritterList from '@/Components/CritterList.vue'
import CritterDetail from '@/Components/CritterDetail.vue'
import EmptyState from '@/Components/EmptyState.vue'

const selectedBug = ref(null)
const availableBugsIds = ref(new Set())
const museumBugsIds = ref(new Set()) 
const museumIconUrl = '/icons/museum.png'

const page = usePage()
// Cambiamos la forma de detectar auth para que sea más robusta
const isAuthenticated = computed(() => {
    return page.props.auth && page.props.auth.user !== null;
})

const { data: bugs } = useFetch('/api/bugs')
const { data: availableBugsData } = useFetch('/api/bugs/available?hemisphere=north')
const { data: userBugsData } = useFetch('/api/user/bugs')

watch(availableBugsData, (newData) => {
  if (newData) availableBugsIds.value = new Set(newData.map(bug => bug.id))
}, { immediate: true })

watch(userBugsData, (newData) => {
  if (Array.isArray(newData)) {
    museumBugsIds.value = new Set(newData.map(bug => bug.id))
  }
}, { immediate: true })

const selectBug = (bug) => {
  selectedBug.value = bug
}

const bugAvailability = computed(() => {
  return selectedBug.value ? availableBugsIds.value.has(selectedBug.value.id) : false
})

const isInMuseum = (bugId) => {
  return museumBugsIds.value.has(bugId)
}

// --- FUNCIÓN DE DONAR CON DEBUG ---
const toggleMuseum = async (bugId) => {
  console.log("Intentando donar bicho ID:", bugId); // Si esto no sale en consola, el problema es el componente hijo
  
  if (!isAuthenticated.value) {
      console.error("No puedes donar: No estás autenticado");
      return;
  }

  try {
    const response = await axios.post(`/bugs/${bugId}/donate`);
    console.log("Respuesta del servidor:", response.data);

    // Cambiamos el estado localmente
    if (museumBugsIds.value.has(bugId)) {
      museumBugsIds.value.delete(bugId);
    } else {
      museumBugsIds.value.add(bugId);
    }
    
    // Forzamos actualización visual
    museumBugsIds.value = new Set(museumBugsIds.value);
    
  } catch (err) {
    console.error("Error Axios:", err.response ? err.response.data : err.message);
  }
}
</script>