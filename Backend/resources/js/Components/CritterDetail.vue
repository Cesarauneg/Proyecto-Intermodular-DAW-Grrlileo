<template>
  <div class="max-w-4xl w-full">
    <div class="bg-white/90 backdrop-blur rounded-3xl shadow-2xl overflow-hidden border-4 border-green-300">
      
      <!-- Header con nombre y botón de museo -->
      <div class="bg-gradient-to-r from-green-400 to-blue-400 p-4 sm:p-6 text-center relative">
  <div v-if="showMuseumButton" class="absolute top-4 right-4 sm:top-6 sm:right-6 z-50">
      <MuseumButton
          :is-in-museum="isInMuseum"
          :museum-icon="museumIconUrl"
          @toggle="$emit('toggleMuseum')"
      />
  </div>

  <h1 
    :class="[
      'text-3xl sm:text-4xl lg:text-5xl font-bold text-white drop-shadow-lg mb-2',
      showMuseumButton ? 'pr-16 sm:pr-20' : 'pr-0'
    ]"
  >
    {{ critter.name_es.charAt(0).toUpperCase() + critter.name_es.slice(1).toLowerCase() }}
  </h1>
  
  <p 
    :class="[
      'text-white/90 text-sm sm:text-base lg:text-lg italic',
      showMuseumButton ? 'pr-16 sm:pr-20' : 'pr-0'
    ]"
  >
    {{ critter.name }}
  </p>
</div>

      <!-- Contenedor principal - RESPONSIVE -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 p-4 sm:p-6 lg:p-8">
        
        <!-- Columna izquierda: Imagen -->
        <div class="flex items-center justify-center">
          <div class="relative w-full">
            <div class="absolute inset-0 bg-green-200 rounded-full blur-2xl opacity-50"></div>
            <img
              :src="critter.image"
              :alt="critter.name_es"
              class="relative w-full h-48 sm:h-64 lg:h-96 object-contain drop-shadow-2xl transform hover:scale-110 transition-transform duration-300"
            />
          </div>
        </div>

        <!-- Columna derecha: Información -->
        <div class="flex flex-col justify-center space-y-3 sm:space-y-4">
          
          <!-- Tarjeta de Precio -->
          <InfoCard color-classes="bg-gradient-to-r from-yellow-100 to-yellow-200 border-yellow-400">
            <div class="flex items-center justify-between">
              <span class="text-lg sm:text-xl lg:text-2xl font-bold text-yellow-800">💰 Precio</span>
              <span class="text-2xl sm:text-2xl lg:text-3xl font-black text-yellow-900">{{ critter.price }}</span>
            </div>
            <p class="text-xs sm:text-sm text-yellow-700 mt-1">bayas</p>
          </InfoCard>

          <!-- Tarjeta de Disponibilidad -->
          <InfoCard :color-classes="isAvailable 
            ? 'bg-gradient-to-r from-green-100 to-emerald-200 border-green-400' 
            : 'bg-gradient-to-r from-gray-100 to-gray-200 border-gray-400'
          ">
            <div class="flex items-center justify-between">
              <span :class="[
                'text-lg sm:text-xl lg:text-2xl font-bold',
                isAvailable ? 'text-green-800' : 'text-gray-800'
              ]">
                {{ isAvailable ? '✅' : '❌' }} Disponibilidad
              </span>
              <span :class="[
                'text-xl sm:text-xl lg:text-2xl font-bold',
                isAvailable ? 'text-green-900' : 'text-gray-900'
              ]">
                {{ isAvailable ? 'Disponible' : 'No disponible' }}
              </span>
            </div>
            <p :class="[
              'text-xs sm:text-sm mt-1',
              isAvailable ? 'text-green-700' : 'text-gray-700'
            ]">
              {{ isAvailable ? 'Puedes atraparlo ahora' : 'No está en temporada' }}
            </p>
          </InfoCard>

          <!-- Tarjeta de Ubicación -->
          <InfoCard color-classes="bg-gradient-to-r from-blue-100 to-cyan-200 border-blue-400">
            <div class="flex items-center justify-between">
              <span class="text-base sm:text-lg lg:text-xl font-bold text-blue-800">📍 Ubicación</span>
              <span class="text-sm sm:text-base lg:text-lg font-bold text-blue-900 text-right">{{ critter.location }}</span>
            </div>
          </InfoCard>

          <!-- Tarjeta de Rareza y Estación -->
          <InfoCard color-classes="bg-gradient-to-r from-purple-100 to-pink-200 border-purple-400">
            <div class="space-y-2 sm:space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-base sm:text-lg lg:text-xl font-bold text-purple-800">✨ Rareza</span>
                <span class="text-sm sm:text-base lg:text-lg font-semibold text-purple-900">{{ critter.rarity }}</span>
              </div>
            </div>
          </InfoCard>

        </div>
      </div>

      <!-- Frase de captura al final -->
      <div v-if="critter.catch_phrase_en" class="bg-gradient-to-r from-amber-50 to-orange-50 border-t-4 border-orange-300 p-4 sm:p-6">
        <div class="flex items-start gap-3">
          <span class="text-3xl sm:text-4xl">💬</span>
          <div class="flex-1">
            <p class="text-xs sm:text-sm font-semibold text-orange-800 mb-1">FRASE DE CAPTURA</p>
            <p class="text-base sm:text-lg lg:text-xl italic text-gray-700 leading-relaxed">
              "{{ critter.catch_phrase_en }}"
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import InfoCard from './InfoCard.vue'
import MuseumButton from './MuseumButton.vue'

defineProps({
  critter: {
    type: Object,
    required: true
  },
  isAvailable: {
    type: Boolean,
    default: false
  },
  isInMuseum: {
    type: Boolean,
    default: false
  },
  museumIconUrl: {
    type: String,
    required: true
  },
  showMuseumButton: {
    type: Boolean,
    default: false
  }
})

defineEmits(['toggleMuseum'])
</script>