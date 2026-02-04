<template>
  <article
    class="ac-card"
    role="button"
    tabindex="0"
    :aria-label="`Ver detalles de ${character.name_es}`"
    @click="$emit('select', character)"
    @keydown.enter="$emit('select', character)"
    @keydown.space.prevent="$emit('select', character)"
  >
    <!-- Botón favorito (solo usuarios logueados) -->
    <div v-if="isAuthenticated" class="absolute top-2 right-2 z-10">
      <FavoriteButton 
        :active="isFavorite(character.id)" 
        @toggle="toggleFavorite(character.id)" 
      />
    </div>

    <!-- Marco exterior -->
    <div class="ac-card__frame">
      <!-- Cabecera con nombre -->
      <header class="ac-card__header">
        <h3 class="ac-card__name">{{ character.name_es }}</h3>
      </header>

      <!-- Imagen del personaje -->
      <figure class="ac-card__image-container">
        <div class="ac-card__image-bg" aria-hidden="true"></div>
        <img
          :src="imagePath"
          :alt="`Imagen de ${character.name_es}, un ${character.species}`"
          class="ac-card__image"
          loading="lazy"
          decoding="async"
        />
      </figure>

      <!-- Info inferior -->
      <footer class="ac-card__footer">
        <div class="ac-card__info">
          <span class="ac-card__personality">{{ character.personality }}</span>
          <span class="ac-card__species">{{ character.species }}</span>
        </div>
        <div class="ac-card__decoration" aria-hidden="true">
          <span class="leaf">🍃</span>
        </div>
      </footer>
    </div>
  </article>
</template>

<script setup>
/**
 * @fileoverview Tarjeta de aldeano con estilo Animal Crossing
 * Muestra información básica del aldeano y es clickeable para ver detalles.
 * Incluye botón de favorito para usuarios autenticados.
 */

import { ref } from 'vue'
import { useFavorites } from '@/Composables/useFavorites'
import FavoriteButton from '@/Components/FavoriteButton.vue'


/**
 * @typedef {Object} Villager
 * @property {number} id - ID único del aldeano
 * @property {string} name_es - Nombre en español
 * @property {string} species - Especie del aldeano
 * @property {string} personality - Tipo de personalidad
 * @property {string} image - URL de la imagen
 */

const props = defineProps({
  /** Datos del aldeano a mostrar */
  character: {
    type: Object,
    required: true,
    validator: (v) => v.id && v.name_es
  },
  /** Indica si el usuario está autenticado */
  isAuthenticated: {
    type: Boolean,
    default: false
  }
})

defineEmits(['select'])

/** Ruta de la imagen del personaje */
const imagePath = `${props.character.image}`

const { isFavorite, toggleFavorite } = useFavorites()

</script>

<style src="@/../css/components/character-card.css"></style>
