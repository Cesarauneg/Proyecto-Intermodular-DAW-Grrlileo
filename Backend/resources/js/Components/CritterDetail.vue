<!--
  @fileoverview Tarjeta de detalle para criaturas/items de la Critterpedia.
  Muestra información completa con imagen, estadísticas y frase de captura.
-->
<template>
  <article
    class="w-full max-w-[95vw] sm:max-w-lg md:max-w-xl lg:max-w-2xl"
    :aria-labelledby="titleId"
  >
    <!-- Botón para abrir menú (solo móvil) -->
    <BackButton
      class="mb-2"
      :aria-label="`Abrir lista de ${itemType}`"
      @click="$emit('back')"
      />

    <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl overflow-hidden border-3 border-green-300">
      <!-- Header con nombre y botón de museo -->
      <header class="bg-gradient-to-r from-green-400 to-blue-400 p-2 sm:p-3 text-center relative">
        <h1
          :id="titleId"
          class="text-lg sm:text-xl lg:text-2xl font-bold text-white drop-shadow-lg"
        >
          {{ displayName }}
        </h1>

        <p
          v-if="englishName"
          class="text-white/90 text-xs sm:text-sm italic"
        >
          {{ englishName }}
        </p>

        <!-- Botón de museo -->
        <div
          v-if="showMuseumButton"
          class="absolute top-0 right-0 bottom-0 p-2 flex items-center"
        >
          <MuseumButton
            :is-in-museum="isInMuseum"
            :museum-icon="museumIconUrl"
            @toggle="$emit('toggleMuseum')"
          />
        </div>
      </header>

      <!-- Contenedor principal - siempre 2 columnas -->
      <div class="grid grid-cols-2 gap-2 sm:gap-3 p-2 sm:p-3">
        <!-- Columna izquierda: Imagen -->
        <figure class="flex items-center justify-center">
          <div class="relative w-full">
            <div
              class="absolute inset-0 bg-green-200 rounded-full blur-xl opacity-50"
              aria-hidden="true"
            />
            <img
              :src="critter.image"
              :alt="`Imagen de ${displayName}`"
              class="relative w-full h-auto max-h-24 sm:max-h-32 lg:max-h-40 object-contain drop-shadow-xl transform hover:scale-105 transition-transform duration-300"
            />
          </div>
        </figure>

        <!-- Columna derecha: Estadísticas -->
        <div
          class="flex flex-col justify-center space-y-1 sm:space-y-1.5"
          role="list"
          :aria-label="`Estadísticas de ${displayName}`"
        >
          <!-- Precio de venta -->
          <StatCard
            v-if="critter.price"
            icon="💰"
            label="Precio"
            :value="critter.price"
            variant="yellow"
            format-number
          />

          <!-- Precio de compra (arte) -->
          <StatCard
            v-if="critter.buy_price"
            icon="💰"
            label="Compra"
            :value="critter.buy_price"
            variant="yellow"
            format-number
          />

          <!-- Precio de venta (arte) -->
          <StatCard
            v-if="critter.sell_price"
            icon="💰"
            label="Venta"
            :value="critter.sell_price"
            variant="blue"
            format-number
          />

          <!-- Disponibilidad -->
          <StatCard
            v-if="showAvailability"
            :icon="isAvailable ? '✅' : '❌'"
            label="Disponible"
            :value="isAvailable ? 'Sí' : 'No'"
            :variant="isAvailable ? 'green' : 'gray'"
          />

          <!-- Falsificación (arte) -->
          <StatCard
            v-if="'has_fake' in critter"
            :icon="critter.has_fake ? '⚠️' : '✅'"
            :label="critter.has_fake ? 'Falsificación' : 'Autenticidad'"
            :value="critter.has_fake ? 'Tiene copia' : 'Auténtico'"
            :variant="critter.has_fake ? 'red' : 'green'"
          />

          <!-- Ubicación -->
          <StatCard
            v-if="critter.location"
            icon="📍"
            label="Ubicación"
            :value="critter.location"
            variant="blue"
          />

          <!-- Parte de (fósiles) -->
          <StatCard
            v-if="critter.part_of"
            icon="🦴"
            label="Parte de"
            :value="capitalize(critter.part_of)"
            variant="amber"
          />

          <!-- Velocidad (criaturas marinas) -->
          <StatCard
            v-if="critter.speed"
            icon="⚡"
            label="Velocidad"
            :value="capitalize(critter.speed)"
            variant="blue"
          />

          <!-- Rareza -->
          <StatCard
            v-if="critter.rarity"
            icon="✨"
            label="Rareza"
            :value="critter.rarity"
            variant="purple"
          />

          <!-- Estación -->
          <StatCard
            v-if="critter.season"
            icon="🌸"
            label="Estación"
            :value="critter.season"
            variant="purple"
          />
        </div>
      </div>

      <!-- Frase de captura -->
      <footer
        v-if="critter.catch_phrase_en"
        class="bg-gradient-to-r from-amber-50 to-orange-50 border-t-2 border-orange-300 p-2 sm:p-3"
      >
        <blockquote class="flex items-start gap-1.5">
          <span aria-hidden="true" class="text-base sm:text-lg">💬</span>
          <div class="flex-1">
            <p class="text-xs sm:text-sm font-semibold text-orange-800 mb-1">
              Frase de captura
            </p>
            <p class="text-xs sm:text-sm italic text-gray-700 leading-snug">
              "{{ critter.catch_phrase_en }}"
            </p>
          </div>
        </blockquote>
      </footer>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import StatCard from './Base/StatCard.vue'
import BackButton from './Base/BackButton.vue'
import MuseumButton from './MuseumButton.vue'
import { capitalize } from '@/Utils/formatters.js'

const props = defineProps({
  /** Objeto con los datos de la criatura */
  critter: {
    type: Object,
    required: true
  },
  /** Si la criatura está disponible actualmente */
  isAvailable: {
    type: Boolean,
    default: false
  },
  /** Si la criatura está en el museo del usuario */
  isInMuseum: {
    type: Boolean,
    default: false
  },
  /** URL del icono del museo */
  museumIconUrl: {
    type: String,
    required: true
  },
  /** Mostrar botón de museo (solo para usuarios autenticados) */
  showMuseumButton: {
    type: Boolean,
    default: false
  },
  /** Mostrar tarjeta de disponibilidad */
  showAvailability: {
    type: Boolean,
    default: true
  },
  /** Tipo de item para accesibilidad */
  itemType: {
    type: String,
    default: 'criaturas'
  }
})

defineEmits(['toggleMuseum', 'back'])

// ID único para vinculación ARIA
const titleId = computed(() => `critter-title-${props.critter.id}`)

// Nombre a mostrar
const displayName = computed(() =>
  capitalize(props.critter.name_es || props.critter.name_en || props.critter.name || 'Sin nombre')
)

// Nombre en inglés (subtítulo)
const englishName = computed(() =>
  props.critter.name_en || props.critter.name
)
</script>
