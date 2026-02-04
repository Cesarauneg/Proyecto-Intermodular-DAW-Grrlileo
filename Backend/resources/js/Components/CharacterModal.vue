<template>
  <div
    class="modal-overlay"
    role="dialog"
    aria-modal="true"
    :aria-labelledby="`modal-title-${character.id}`"
    @click="$emit('close')"
    @keydown.escape="$emit('close')"
  >
    <article class="ac-modal" @click.stop>
      <!-- Marco exterior -->
      <div class="ac-modal__frame">
        <!-- Botón cerrar -->
        <button
          class="ac-modal__close"
          @click="$emit('close')"
          aria-label="Cerrar modal"
          type="button"
        >
          <span aria-hidden="true">✕</span>
        </button>

        <!-- Cabecera con nombre -->
        <header class="ac-modal__header">
          <h3 :id="`modal-title-${character.id}`" class="ac-modal__name">
            {{ character.name_es }}
          </h3>
        </header>

        <!-- Imagen del personaje -->
        <figure class="ac-modal__image-container">
          <div class="ac-modal__image-bg" aria-hidden="true"></div>
          <img
            :src="character.image"
            :alt="`Imagen de ${character.name_es}`"
            class="ac-modal__image"
            decoding="async"
          />
        </figure>

        <!-- Frase del personaje -->
        <blockquote class="ac-modal__quote">
          <span class="ac-modal__quote-icon" aria-hidden="true">💬</span>
          <p>"{{ character.catch_es }}"</p>
        </blockquote>

        <!-- Info del personaje -->
        <dl class="ac-modal__info">
          <div class="ac-modal__info-row">
            <dt class="ac-modal__info-label">
              <span class="ac-modal__info-icon" aria-hidden="true">🐾</span>
              Especie
            </dt>
            <dd class="ac-modal__info-value">{{ character.species }}</dd>
          </div>
          <div class="ac-modal__info-row">
            <dt class="ac-modal__info-label">
              <span class="ac-modal__info-icon" aria-hidden="true">🎭</span>
              Personalidad
            </dt>
            <dd class="ac-modal__info-value">{{ character.personality }}</dd>
          </div>
          <div class="ac-modal__info-row">
            <dt class="ac-modal__info-label">
              <span class="ac-modal__info-icon" aria-hidden="true">⚧</span>
              Género
            </dt>
            <dd class="ac-modal__info-value">{{ character.gender }}</dd>
          </div>
          <div class="ac-modal__info-row">
            <dt class="ac-modal__info-label">
              <span class="ac-modal__info-icon" aria-hidden="true">🎂</span>
              Cumpleaños
            </dt>
            <dd class="ac-modal__info-value">{{ character.birthday_string }}</dd>
          </div>
          <div class="ac-modal__info-row">
            <dt class="ac-modal__info-label">
              <span class="ac-modal__info-icon" aria-hidden="true">⭐</span>
              Hobby
            </dt>
            <dd class="ac-modal__info-value">{{ character.hobby }}</dd>
          </div>
          <div class="ac-modal__info-row">
            <dt class="ac-modal__info-label">
              <span class="ac-modal__info-icon" aria-hidden="true">💭</span>
              Frase
            </dt>
            <dd class="ac-modal__info-value">{{ character.saying }}</dd>
          </div>
        </dl>

        <!-- Decoración inferior -->
        <footer class="ac-modal__footer" aria-hidden="true">
          <span class="leaf">🍃</span>
          <span class="leaf">🌸</span>
          <span class="leaf">🍃</span>
        </footer>
      </div>
    </article>
  </div>
</template>

<script setup>
/**
 * @fileoverview Modal de detalles de aldeano con estilo Animal Crossing.
 * Muestra información completa del aldeano seleccionado.
 */

/**
 * @typedef {Object} Villager
 * @property {number} id - ID único del aldeano
 * @property {string} name_es - Nombre en español
 * @property {string} species - Especie del aldeano
 * @property {string} personality - Tipo de personalidad
 * @property {string} gender - Género del aldeano
 * @property {string} image - URL de la imagen
 * @property {string} catch_es - Frase característica en español
 * @property {string} birthday_string - Fecha de cumpleaños
 * @property {string} hobby - Hobby del aldeano
 * @property {string} saying - Frase del aldeano
 */

defineProps({
  /** Datos del aldeano a mostrar */
  character: {
    type: Object,
    required: true
  }
})

defineEmits(['close'])
</script>

<style src="@/../css/components/character-modal.css"></style>
