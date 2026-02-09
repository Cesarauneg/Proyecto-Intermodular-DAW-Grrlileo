<template>
  <div class="min-h-screen bg-gradient-to-br from-amber-50 to-yellow-50 p-4 sm:p-6 lg:p-8">
    
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8">
      <h1 class="text-4xl sm:text-5xl font-bold text-center text-amber-900 mb-2">
        🦴 Galería de Fósiles
      </h1>
      <p class="text-center text-amber-700 text-lg">
        {{ fossils?.length || 0 }} fósiles descubiertos
      </p>
    </div>

    <!-- Grid de fósiles -->
    <div class="max-w-7xl mx-auto">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-6">
        
        <div
          v-for="fossil in fossils || []"
          :key="fossil.id"
          @click="selectFossil(fossil)"
          class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 cursor-pointer overflow-hidden border-2 border-amber-200 hover:border-amber-400"
        >
          <!-- Imagen del fósil -->
          <div class="aspect-square p-4 bg-gradient-to-br from-amber-50 to-yellow-50 flex items-center justify-center">
            <img
              :src="fossil.image"
              :alt="fossil.name_es || fossil.name_en"
              class="w-full h-full object-contain drop-shadow-lg"
            />
          </div>

          <!-- Info del fósil -->
          <div class="p-3 bg-white border-t-2 border-amber-100">
            <h3 class="font-bold text-sm sm:text-base text-center text-gray-800 truncate mb-1">
              {{ capitalize(fossil.name_es || fossil.name_en) }}
            </h3>
            
            <div class="flex items-center justify-center gap-2 text-amber-700">
              <span class="text-lg font-bold">💰</span>
              <span class="font-semibold text-sm">{{ fossil.price }}</span>
            </div>

            <!-- Part of (si existe) -->
            <p v-if="fossil.part_of" class="text-xs text-center text-gray-500 mt-1 truncate">
              {{ capitalize(fossil.part_of) }}
            </p>

            <!-- Botón de museo (solo si está autenticado) -->
            <button
              v-if="isAuthenticated"
              @click.stop="toggleMuseum(fossil.id)"
              :class="[
                'w-full mt-2 py-1.5 rounded-lg font-semibold text-xs transition-all duration-200',
                isInMuseum(fossil.id)
                  ? 'bg-gradient-to-r from-amber-400 to-orange-500 text-white hover:from-amber-500 hover:to-orange-600'
                  : 'bg-gray-200 text-gray-600 hover:bg-gray-300'
              ]"
            >
              {{ isInMuseum(fossil.id) ? '✓ En museo' : '+ Agregar' }}
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Modal de detalles -->
    <Teleport to="body">
      <Transition name="modal">
        <div
          v-if="selectedFossil"
          @click="selectedFossil = null"
          class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        >
          <div
            @click.stop
            class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
          >
            <!-- Header del modal -->
            <div class="bg-gradient-to-r from-amber-400 to-orange-400 p-6 relative">
              <button
                @click="selectedFossil = null"
                class="absolute top-4 right-4 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white text-xl transition-all"
              >
                ✕
              </button>
              
              <h2 class="text-3xl sm:text-4xl font-bold text-white text-center pr-12">
                {{ capitalize(selectedFossil.name_es || selectedFossil.name_en) }}
              </h2>
              <p v-if="selectedFossil.name_en" class="text-white/90 text-center mt-1 italic">
                {{ selectedFossil.name_en }}
              </p>
            </div>

            <!-- Contenido del modal -->
            <div class="p-6">
              <!-- Imagen grande -->
              <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl p-8 mb-6">
                <img
                  :src="selectedFossil.image"
                  :alt="selectedFossil.name_es || selectedFossil.name_en"
                  class="w-full max-h-80 object-contain drop-shadow-2xl"
                />
              </div>

              <!-- Información -->
              <div class="space-y-4">
                <!-- Precio -->
                <div class="bg-gradient-to-r from-yellow-100 to-yellow-200 rounded-xl p-4 border-2 border-yellow-400">
                  <div class="flex items-center justify-between">
                    <span class="text-xl font-bold text-yellow-800">💰 Precio</span>
                    <span class="text-2xl font-black text-yellow-900">{{ selectedFossil.price }}</span>
                  </div>
                  <p class="text-sm text-yellow-700 mt-1">bayas</p>
                </div>

                <!-- Part of -->
                <div v-if="selectedFossil.part_of" class="bg-gradient-to-r from-amber-100 to-orange-200 rounded-xl p-4 border-2 border-amber-400">
                  <div class="flex items-center justify-between">
                    <span class="text-xl font-bold text-amber-800">🦴 Parte de</span>
                    <span class="text-lg font-bold text-amber-900">{{ capitalize(selectedFossil.part_of) }}</span>
                  </div>
                </div>

                <!-- Botón de museo -->
                <button
                  v-if="isAuthenticated"
                  @click="toggleMuseum(selectedFossil.id)"
                  :class="[
                    'w-full py-4 rounded-xl font-bold text-lg transition-all duration-200 transform hover:scale-105',
                    isInMuseum(selectedFossil.id)
                      ? 'bg-gradient-to-r from-amber-400 to-orange-500 text-white hover:from-amber-500 hover:to-orange-600'
                      : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  ]"
                >
                  {{ isInMuseum(selectedFossil.id) ? '✓ En el museo' : '+ Agregar al museo' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useFetch } from '@/Composables/useFetch.js'
import { capitalize } from '@/Utils/formatters.js'

const selectedFossil = ref(null)
const museumFossilIds = ref(new Set())
const isAuthenticated = ref(true) // Cambiar según tu auth

// Traer fósiles
const { data: fossils } = useFetch('api/fossils')

const selectFossil = (fossil) => {
  selectedFossil.value = fossil
}

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

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
