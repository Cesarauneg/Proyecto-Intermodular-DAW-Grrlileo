<template>
  <div class="flex flex-col lg:flex-row h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <!-- Lista de bichos -->
    <div class="w-full lg:w-1/4 bg-white/80 backdrop-blur border-b-4 lg:border-b-0 lg:border-r-4 border-green-300 p-4 flex flex-col h-64 lg:h-full shadow-lg">
      <h2 class="text-xl lg:text-2xl font-bold mb-4 text-green-800 text-center pb-2 border-b-2 border-green-300">
        Todos los bichos
      </h2>

      <!-- Scroll solo en la lista -->
      <div class="flex-1 overflow-y-auto">
        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-3 gap-2">
          <div
            v-for="bug in bugs || []"
            :key="bug.id"
            @click="selectBug(bug)"
            :class="[
              'cursor-pointer p-2 rounded-lg transition-all duration-200 transform hover:scale-105 relative',
              selectedBug && selectedBug.id === bug.id 
                ? 'bg-green-200 border-2 border-green-500 shadow-lg'
                : 'bg-white border-2 border-gray-200 hover:border-green-400 hover:shadow-md'
            ]"
          >
            <!-- Indicador de disponibilidad -->
            <div v-if="isBugAvailable(bug.id)" class="absolute top-1 right-1 w-3 h-3 bg-green-500 rounded-full border border-white shadow"></div>
            
            <!-- Imagen miniatura -->
            <img
              :src="bug.icon"
              :alt="bug.name_es"
              class="w-full h-12 sm:h-14 lg:h-16 object-contain mb-1"
            />
            <!-- Nombre -->
            <p class="text-xs text-center truncate font-medium">{{ bug.name_es }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Detalles del bicho - CENTRADO Y RESPONSIVE -->
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-y-auto">
      <div v-if="selectedBug" class="max-w-4xl w-full">
        <div class="bg-white/90 backdrop-blur rounded-3xl shadow-2xl overflow-hidden border-4 border-green-300">
          
          <!-- Header con nombre -->
          <div class="bg-gradient-to-r from-green-400 to-blue-400 p-4 sm:p-6 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white drop-shadow-lg mb-2">
              {{ selectedBug.name_es }}
            </h1>
            <p class="text-white/90 text-sm sm:text-base lg:text-lg italic">{{ selectedBug.name }}</p>
          </div>

          <!-- Contenedor principal - RESPONSIVE -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 p-4 sm:p-6 lg:p-8">
            
            <!-- Columna izquierda: Imagen -->
            <div class="flex items-center justify-center">
              <div class="relative w-full">
                <div class="absolute inset-0 bg-green-200 rounded-full blur-2xl opacity-50"></div>
                <img
                  :src="selectedBug.image"
                  :alt="selectedBug.name_es"
                  class="relative w-full h-48 sm:h-64 lg:h-96 object-contain drop-shadow-2xl transform hover:scale-110 transition-transform duration-300"
                />
              </div>
            </div>

            <!-- Columna derecha: Información -->
            <div class="flex flex-col justify-center space-y-3 sm:space-y-4">
              
              <!-- Tarjeta de Precio -->
              <div class="bg-gradient-to-r from-yellow-100 to-yellow-200 rounded-xl sm:rounded-2xl p-4 sm:p-6 border-2 border-yellow-400 shadow-lg transform hover:scale-105 transition-transform">
                <div class="flex items-center justify-between">
                  <span class="text-lg sm:text-xl lg:text-2xl font-bold text-yellow-800">💰 Precio</span>
                  <span class="text-2xl sm:text-2xl lg:text-3xl font-black text-yellow-900">{{ selectedBug.price }}</span>
                </div>
                <p class="text-xs sm:text-sm text-yellow-700 mt-1">bayas</p>
              </div>

              <!-- Tarjeta de Disponibilidad -->
              <div :class="[
                'rounded-xl sm:rounded-2xl p-4 sm:p-6 border-2 shadow-lg transform hover:scale-105 transition-transform',
                bugAvailability ? 'bg-gradient-to-r from-green-100 to-emerald-200 border-green-400' : 'bg-gradient-to-r from-gray-100 to-gray-200 border-gray-400'
              ]">
                <div class="flex items-center justify-between">
                  <span :class="[
                    'text-lg sm:text-xl lg:text-2xl font-bold',
                    bugAvailability ? 'text-green-800' : 'text-gray-800'
                  ]">
                    {{ bugAvailability ? '✅' : '❌' }} Disponibilidad
                  </span>
                  <span :class="[
                    'text-xl sm:text-xl lg:text-2xl font-bold',
                    bugAvailability ? 'text-green-900' : 'text-gray-900'
                  ]">
                    {{ bugAvailability ? 'Disponible' : 'No disponible' }}
                  </span>
                </div>
                <p :class="[
                  'text-xs sm:text-sm mt-1',
                  bugAvailability ? 'text-green-700' : 'text-gray-700'
                ]">
                  {{ bugAvailability ? 'Puedes atraparlo ahora' : 'No está en temporada' }}
                </p>
              </div>

              <!-- Tarjeta de Ubicación -->
              <div class="bg-gradient-to-r from-blue-100 to-cyan-200 rounded-xl sm:rounded-2xl p-4 sm:p-6 border-2 border-blue-400 shadow-lg transform hover:scale-105 transition-transform">
                <div class="flex items-center justify-between">
                  <span class="text-base sm:text-lg lg:text-xl font-bold text-blue-800">📍 Ubicación</span>
                  <span class="text-sm sm:text-base lg:text-lg font-bold text-blue-900 text-right">{{ selectedBug.location }}</span>
                </div>
              </div>

              <!-- Tarjeta de Rareza y Estación -->
              <div class="bg-gradient-to-r from-purple-100 to-pink-200 rounded-xl sm:rounded-2xl p-4 sm:p-6 border-2 border-purple-400 shadow-lg">
                <div class="space-y-2 sm:space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-base sm:text-lg lg:text-xl font-bold text-purple-800">✨ Rareza</span>
                    <span class="text-sm sm:text-base lg:text-lg font-semibold text-purple-900">{{ selectedBug.rarity }}</span>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>

          <!-- Frase de captura al final -->
          <div v-if="selectedBug.catch_phrase_en" class="bg-gradient-to-r from-amber-50 to-orange-50 border-t-4 border-orange-300 p-4 sm:p-6">
            <div class="flex items-start gap-3">
              <span class="text-3xl sm:text-4xl">💬</span>
              <div class="flex-1">
                <p class="text-xs sm:text-sm font-semibold text-orange-800 mb-1">FRASE DE CAPTURA</p>
                <p class="text-base sm:text-lg lg:text-xl italic text-gray-700 leading-relaxed">
                  "{{ selectedBug.catch_phrase_en }}"
                </p>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Mensaje cuando no hay bicho seleccionado -->
      <div v-else class="text-center">
        <div class="text-6xl sm:text-7xl lg:text-8xl mb-4">🦋</div>
        <p class="text-2xl sm:text-2xl lg:text-3xl text-gray-400 font-medium">Selecciona un bicho</p>
        <p class="text-lg sm:text-xl text-gray-300 mt-2">para ver su información</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useFetch } from '@/Composables/useFetch.js'

const selectedBug = ref(null)
const availableBugsIds = ref(new Set())

// Traemos los bichos
const { data: bugs, loading, error } = useFetch('/api/bugs')

// Traemos los bichos disponibles (puedes cambiar 'north' por 'south' si lo necesitas)
const { data: availableBugsData } = useFetch('/api/bugs/available?hemisphere=north')

// Vigilar cuando se carguen los bichos disponibles
watch(availableBugsData, (newData) => {
  if (newData && Array.isArray(newData)) {
    // Crear un Set con los IDs de los bichos disponibles para búsqueda rápida
    availableBugsIds.value = new Set(newData.map(bug => bug.id))
  }
}, { immediate: true })

// Función para seleccionar un bicho
const selectBug = (bug) => {
  selectedBug.value = bug
}

// Función para verificar si un bicho está disponible (usada en la lista)
const isBugAvailable = (bugId) => {
  return availableBugsIds.value.has(bugId)
}

// Computed para saber si el bicho seleccionado está disponible
const bugAvailability = computed(() => {
  if (!selectedBug.value) return false
  return availableBugsIds.value.has(selectedBug.value.id)
})
</script>