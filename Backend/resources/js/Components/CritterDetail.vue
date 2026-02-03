<template>
  <article class="max-w-4xl w-full" aria-labelledby="critter-name">
    <div class="bg-white/90 backdrop-blur rounded-3xl shadow-2xl overflow-hidden border-4 border-green-300">

      <!-- Header con nombre y botón de museo -->
      <header class="bg-gradient-to-r from-green-400 to-blue-400 p-4 sm:p-6 text-center relative">
        <div v-if="showMuseumButton" class="absolute top-4 right-4 sm:top-6 sm:right-6 z-50">
          <MuseumButton
            :is-in-museum="isInMuseum"
            :museum-icon="museumIconUrl"
            @toggle="$emit('toggleMuseum')"
          />
        </div>

        <h1
          id="critter-name"
          :class="titleClasses"
        >
          {{ capitalize(critter.name_es) }}
        </h1>

        <p :class="subtitleClasses">
          <span class="sr-only">Nombre en inglés:</span>
          {{ critter.name }}
        </p>
      </header>

      <!-- Contenedor principal - RESPONSIVE -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 p-4 sm:p-6 lg:p-8">

        <!-- Columna izquierda: Imagen -->
        <figure class="flex items-center justify-center">
          <div class="relative w-full">
            <div
              class="absolute inset-0 bg-green-200 rounded-full blur-2xl opacity-50"
              aria-hidden="true"
            ></div>
            <img
              :src="critter.image"
              :alt="`Imagen detallada de ${critter.name_es}`"
              class="relative w-full h-48 sm:h-64 lg:h-96 object-contain drop-shadow-2xl transform hover:scale-110 transition-transform duration-300"
              loading="lazy"
            />
          </div>
          <figcaption class="sr-only">{{ critter.name_es }}</figcaption>
        </figure>

        <!-- Columna derecha: Información -->
        <section class="flex flex-col justify-center space-y-3 sm:space-y-4" aria-label="Información de la criatura">

          <!-- Tarjeta de Precio -->
          <InfoCard color-classes="bg-gradient-to-r from-yellow-100 to-yellow-200 border-yellow-400">
            <dl class="flex items-center justify-between">
              <dt class="text-lg sm:text-xl lg:text-2xl font-bold text-yellow-800">
                <span aria-hidden="true">💰</span> Precio
              </dt>
              <dd class="text-2xl sm:text-2xl lg:text-3xl font-black text-yellow-900">
                {{ critter.price }}
                <span class="text-xs sm:text-sm text-yellow-700 font-normal block text-right">bayas</span>
              </dd>
            </dl>
          </InfoCard>

          <!-- Tarjeta de Disponibilidad -->
          <InfoCard :color-classes="availabilityCardClasses">
            <dl>
              <div class="flex items-center justify-between">
                <dt :class="availabilityLabelClasses">
                  <span aria-hidden="true">{{ isAvailable ? '✅' : '❌' }}</span> Disponibilidad
                </dt>
                <dd :class="availabilityValueClasses">
                  {{ isAvailable ? 'Disponible' : 'No disponible' }}
                </dd>
              </div>
              <dd :class="availabilityHintClasses">
                {{ isAvailable ? 'Puedes atraparlo ahora' : 'No está en temporada' }}
              </dd>
            </dl>
          </InfoCard>

          <!-- Tarjeta de Ubicación -->
          <InfoCard color-classes="bg-gradient-to-r from-blue-100 to-cyan-200 border-blue-400">
            <dl class="flex items-center justify-between">
              <dt class="text-base sm:text-lg lg:text-xl font-bold text-blue-800">
                <span aria-hidden="true">📍</span> Ubicación
              </dt>
              <dd class="text-sm sm:text-base lg:text-lg font-bold text-blue-900 text-right">
                {{ critter.location }}
              </dd>
            </dl>
          </InfoCard>

          <!-- Tarjeta de Rareza -->
          <InfoCard color-classes="bg-gradient-to-r from-purple-100 to-pink-200 border-purple-400">
            <dl class="flex items-center justify-between">
              <dt class="text-base sm:text-lg lg:text-xl font-bold text-purple-800">
                <span aria-hidden="true">✨</span> Rareza
              </dt>
              <dd class="text-sm sm:text-base lg:text-lg font-semibold text-purple-900">
                {{ critter.rarity }}
              </dd>
            </dl>
          </InfoCard>

        </section>
      </div>

      <!-- Frase de captura al final -->
      <footer
        v-if="critter.catch_phrase_en"
        class="bg-gradient-to-r from-amber-50 to-orange-50 border-t-4 border-orange-300 p-4 sm:p-6"
      >
        <figure class="flex items-start gap-3">
          <span class="text-3xl sm:text-4xl" aria-hidden="true">💬</span>
          <blockquote class="flex-1">
            <figcaption class="text-xs sm:text-sm font-semibold text-orange-800 mb-1">
              Frase de captura
            </figcaption>
            <p class="text-base sm:text-lg lg:text-xl italic text-gray-700 leading-relaxed">
              "{{ critter.catch_phrase_en }}"
            </p>
          </blockquote>
        </figure>
      </footer>

    </div>
  </article>
</template>

<script setup>
/**
 * @fileoverview Vista detallada de una criatura.
 * Muestra información completa de peces, bichos o criaturas marinas.
 *
 * @description Componente que presenta todos los detalles de una criatura
 * incluyendo imagen grande, precio, disponibilidad, ubicación, rareza
 * y frase de captura.
 */

import { computed } from 'vue'
import InfoCard from './InfoCard.vue'
import MuseumButton from './MuseumButton.vue'
import { capitalize } from '@/Utils/formatters.js'

/**
 * @typedef {Object} Critter
 * @property {number} id - ID único
 * @property {string} name - Nombre en inglés
 * @property {string} name_es - Nombre en español
 * @property {string} image - URL de la imagen grande
 * @property {number} price - Precio en bayas
 * @property {string} location - Ubicación donde encontrarla
 * @property {string} rarity - Nivel de rareza
 * @property {string} [catch_phrase_en] - Frase de captura en inglés
 */

const props = defineProps({
  /** @type {Critter} Datos completos de la criatura */
  critter: {
    type: Object,
    required: true
  },
  /** Si la criatura está disponible actualmente */
  isAvailable: {
    type: Boolean,
    default: false
  },
  /** Si la criatura ya está en el museo del usuario */
  isInMuseum: {
    type: Boolean,
    default: false
  },
  /** URL del icono del museo */
  museumIconUrl: {
    type: String,
    required: true
  },
  /** Si se debe mostrar el botón de museo (usuario autenticado) */
  showMuseumButton: {
    type: Boolean,
    default: false
  }
})

defineEmits(['toggleMuseum'])

// ============================================
// Computed properties para clases dinámicas
// ============================================

/**
 * Clases para el título principal
 */
const titleClasses = computed(() => [
  'text-3xl sm:text-4xl lg:text-5xl font-bold text-white drop-shadow-lg mb-2',
  props.showMuseumButton ? 'pr-16 sm:pr-20' : 'pr-0'
])

/**
 * Clases para el subtítulo (nombre en inglés)
 */
const subtitleClasses = computed(() => [
  'text-white/90 text-sm sm:text-base lg:text-lg italic',
  props.showMuseumButton ? 'pr-16 sm:pr-20' : 'pr-0'
])

/**
 * Clases para la tarjeta de disponibilidad
 */
const availabilityCardClasses = computed(() => {
  return props.isAvailable
    ? 'bg-gradient-to-r from-green-100 to-emerald-200 border-green-400'
    : 'bg-gradient-to-r from-gray-100 to-gray-200 border-gray-400'
})

/**
 * Clases para la etiqueta de disponibilidad
 */
const availabilityLabelClasses = computed(() => [
  'text-lg sm:text-xl lg:text-2xl font-bold',
  props.isAvailable ? 'text-green-800' : 'text-gray-800'
])

/**
 * Clases para el valor de disponibilidad
 */
const availabilityValueClasses = computed(() => [
  'text-xl sm:text-xl lg:text-2xl font-bold',
  props.isAvailable ? 'text-green-900' : 'text-gray-900'
])

/**
 * Clases para el texto de ayuda de disponibilidad
 */
const availabilityHintClasses = computed(() => [
  'text-xs sm:text-sm mt-1',
  props.isAvailable ? 'text-green-700' : 'text-gray-700'
])
</script>
