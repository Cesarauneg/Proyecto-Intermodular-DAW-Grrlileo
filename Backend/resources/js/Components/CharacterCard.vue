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
    <button
      v-if="isAuthenticated"
      type="button"
      class="ac-card__favorite"
      :class="{ 'ac-card__favorite--active': isFavorite }"
      :aria-label="isFavorite ? 'Quitar de favoritos' : 'Añadir a favoritos'"
      :aria-pressed="isFavorite"
      @click.stop="toggleFavorite"
      @keydown.enter.stop="toggleFavorite"
      @keydown.space.stop.prevent="toggleFavorite"
    >
      <svg
        class="ac-card__favorite-icon"
        viewBox="0 0 24 24"
        fill="currentColor"
        aria-hidden="true"
      >
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
      </svg>
    </button>

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

/** Estado local del favorito (temporal, no persiste) */
const isFavorite = ref(false)

/** Alterna el estado de favorito */
const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
}
</script>

<style src="@/../css/components/character-card.css"></style>
