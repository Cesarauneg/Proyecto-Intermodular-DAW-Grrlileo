<template>
  <div class="max-w-4xl w-full">
    <div class="bg-white/90 backdrop-blur rounded-3xl shadow-2xl overflow-hidden border-4 border-green-300">
      
      <!-- Header con nombre y botón de museo -->
      <div class="bg-gradient-to-r from-green-400 to-blue-400 p-4 sm:p-6 text-center relative">
        <!-- Botón de museo (solo para usuarios autenticados) -->
        <div v-if="showMuseumButton" class="absolute top-4 right-4 sm:top-6 sm:right-6">
          <MuseumButton
            :is-in-museum="isInMuseum"
            :museum-icon="museumIconUrl"
            @toggle="$emit('toggleMuseum')"
          />
        </div>

        <h1 :class="[
          'text-3xl sm:text-4xl lg:text-5xl font-bold text-white drop-shadow-lg mb-2',
          showMuseumButton ? 'pr-16 sm:pr-20' : ''
        ]">
          {{ displayName }}
        </h1>
        <p v-if="critter.name_en || critter.name" class="text-white/90 text-sm sm:text-base lg:text-lg italic">
          {{ critter.name_en || critter.name }}
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
              :alt="displayName"
              class="relative w-full h-48 sm:h-64 lg:h-96 object-contain drop-shadow-2xl transform hover:scale-110 transition-transform duration-300"
            />
          </div>
        </div>

        <!-- Columna derecha: Información -->
        <div class="flex flex-col justify-center space-y-3 sm:space-y-4">
          
          <!-- Tarjeta de Precio (siempre se muestra) -->
          <InfoCard v-if="critter.price" color-classes="bg-gradient-to-r from-yellow-100 to-yellow-200 border-yellow-400">
            <div class="flex items-center justify-between">
              <span class="text-lg sm:text-xl lg:text-2xl font-bold text-yellow-800">💰 Precio</span>
              <span class="text-2xl sm:text-2xl lg:text-3xl font-black text-yellow-900">{{ critter.price }}</span>
            </div>
            <p class="text-xs sm:text-sm text-yellow-700 mt-1">bayas</p>
          </InfoCard>

          <!-- Tarjeta de Precio Compra (siempre se muestra) -->
          <InfoCard v-if="critter.buy_price" color-classes="bg-gradient-to-r from-yellow-100 to-yellow-200 border-yellow-400">
            <div class="flex items-center justify-between">
              <span class="text-lg sm:text-xl lg:text-2xl font-bold text-yellow-800">💰 Precio Compra</span>
              <span class="text-sm sm:text-base lg:text-lg font-bold text-blue-900 text-right">{{ critter.buy_price }}</span>
            </div>
            <p class="text-xs sm:text-sm text-yellow-700 mt-1">bayas</p>
          </InfoCard>

          <!-- Tarjeta de Precio Venta (siempre se muestra) -->
          <InfoCard v-if="critter.sell_price" color-classes="bg-gradient-to-r from-blue-100 to-cyan-200 border-blue-400">
            <div class="flex items-center justify-between">
              <span class="text-base sm:text-lg lg:text-xl font-bold text-blue-800">💰 Precio Venta</span>
              <span class="text-sm sm:text-base lg:text-lg font-bold text-blue-900 text-right">{{ critter.sell_price }}</span>
            </div>
            <p class="text-xs sm:text-sm mt-1 text-blue-700">bayas</p>
          </InfoCard>

          <!-- Tarjeta de Disponibilidad (solo si showAvailability es true) -->
          <InfoCard 
            v-if="showAvailability"
            :color-classes="isAvailable 
              ? 'bg-gradient-to-r from-green-100 to-emerald-200 border-green-400' 
              : 'bg-gradient-to-r from-gray-100 to-gray-200 border-gray-400'
            "
          >
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

          <!-- Tarjeta de Falsificaciones (solo si showAvailability es true) -->
          <InfoCard
            v-if="'has_fake' in critter"
            :color-classes="critter.has_fake
            ? 'bg-gradient-to-r from-red-100 to-orange-200 border-red-400'
            : 'bg-gradient-to-r from-green-100 to-emerald-200 border-green-400'
            "
          >
            <div class="flex items-center justify-between">
              <span class="text-lg sm:text-xl lg:text-2xl font-bold">
                {{ critter.has_fake ? '❌ Falsificación' : '✅ Autenticidad' }}
              </span>
              <span class="text-xl sm:text-xl lg:text-2xl font-bold">
                {{ critter.has_fake ? 'Tiene falsificación' : 'No tiene falsificación' }}
              </span>
            </div>
            <p class="text-xs sm:text-sm mt-1">
              {{ critter.has_fake
                ? 'Cuidado, puede ser una copia'
                : 'Esta obra es auténtica'
              }}
            </p>
          </InfoCard>


          <!-- Tarjeta de Ubicación (solo si existe location) -->
          <InfoCard v-if="critter.location" color-classes="bg-gradient-to-r from-blue-100 to-cyan-200 border-blue-400">
            <div class="flex items-center justify-between">
              <span class="text-base sm:text-lg lg:text-xl font-bold text-blue-800">📍 Ubicación</span>
              <span class="text-sm sm:text-base lg:text-lg font-bold text-blue-900 text-right">{{ critter.location }}</span>
            </div>
          </InfoCard>

          <!-- Tarjeta de Part Of (solo si existe part_of - para fósiles) -->
          <InfoCard v-if="critter.part_of" color-classes="bg-gradient-to-r from-amber-100 to-yellow-200 border-amber-400">
            <div class="flex items-center justify-between">
              <span class="text-base sm:text-lg lg:text-xl font-bold text-amber-800">🦴 Parte de</span>
              <span class="text-sm sm:text-base lg:text-lg font-bold text-amber-900 text-right">{{ capitalize(critter.part_of) }}</span>
            </div>
          </InfoCard>

          <InfoCard v-if="critter.speed" color-classes="bg-gradient-to-r from-blue-100 to-cyan-200 border-blue-400">
            <div class="flex items-center justify-between">
              <span class="text-base sm:text-lg lg:text-xl font-bold text-blue-800">⚡ Velocidad</span>
              <span class="text-sm sm:text-base lg:text-lg font-bold text-blue-900 text-right">{{ capitalize(critter.speed) }}</span>
            </div>
          </InfoCard>

          <!-- Tarjeta de Rareza y Estación (solo si existen) -->
          <InfoCard v-if="critter.rarity || critter.season" color-classes="bg-gradient-to-r from-purple-100 to-pink-200 border-purple-400">
            <div class="space-y-2 sm:space-y-3">
              <div v-if="critter.rarity" class="flex items-center justify-between">
                <span class="text-base sm:text-lg lg:text-xl font-bold text-purple-800">✨ Rareza</span>
                <span class="text-sm sm:text-base lg:text-lg font-semibold text-purple-900">{{ critter.rarity }}</span>
              </div>
              <div v-if="critter.rarity && critter.season" class="h-px bg-purple-300"></div>
              <div v-if="critter.season" class="flex items-center justify-between">
                <span class="text-base sm:text-lg lg:text-xl font-bold text-purple-800">🌸 Estación</span>
                <span class="text-sm sm:text-base lg:text-lg font-semibold text-purple-900">{{ critter.season }}</span>
              </div>
            </div>
          </InfoCard>

        </div>
      </div>

      <!-- Frase de captura al final (solo si existe) -->
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
import { computed } from 'vue'
import InfoCard from './InfoCard.vue'
import MuseumButton from './MuseumButton.vue'
import { capitalize } from '@/Utils/formatters.js'

const props = defineProps({
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
  },
  showAvailability: {
    type: Boolean,
    default: true 
  }
})

defineEmits(['toggleMuseum'])

// Computed para obtener el nombre a mostrar
const displayName = computed(() => {
  return capitalize(props.critter.name_es || props.critter.name_en || props.critter.name || 'Sin nombre')
})

</script>