<script setup>
/**
 * @fileoverview Carrusel infinito de aldeanos con efecto marquee.
 * Muestra tarjetas de aldeanos en un scroll horizontal continuo.
 *
 * @description Los datos de aldeanos provienen de Laravel vía Inertia props
 * en Welcome.vue (prop randomVillagers desde VillagerController).
 *
 * Técnica: Se duplica la lista para crear un loop infinito visual.
 * La segunda copia está marcada con aria-hidden para accesibilidad.
 */

/**
 * @typedef {Object} Villager
 * @property {number} id - ID único del aldeano
 * @property {string} name_es - Nombre en español
 * @property {string} species - Especie del aldeano
 * @property {string} image - URL de la imagen
 */

defineProps({
  /** @type {Villager[]} Lista de aldeanos a mostrar */
  villagers: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <section
    v-if="villagers && villagers.length > 0"
    class="marquee-container"
    aria-label="Carrusel de aldeanos"
    aria-roledescription="carrusel"
  >
    <ul class="marquee-track" role="list" aria-live="off">
      <!-- Primera copia: visible para lectores de pantalla -->
      <li
        v-for="villager in villagers"
        :key="'a-' + villager.id"
        class="villager-card"
      >
        <article :aria-label="`${villager.name_es}, ${villager.species}`">
          <figure class="card-image-wrapper">
            <img
              :src="villager.image"
              :alt="`Imagen de ${villager.name_es}`"
              class="card-image"
              loading="lazy"
              decoding="async"
            />
          </figure>
          <div class="card-info">
            <h3 class="card-name">{{ villager.name_es }}</h3>
            <p class="card-species">{{ villager.species }}</p>
          </div>
        </article>
      </li>

      <!-- Segunda copia: decorativa para loop infinito -->
      <li
        v-for="villager in villagers"
        :key="'b-' + villager.id"
        class="villager-card"
        aria-hidden="true"
      >
        <article>
          <figure class="card-image-wrapper">
            <img
              :src="villager.image"
              alt=""
              class="card-image"
              loading="lazy"
              decoding="async"
            />
          </figure>
          <div class="card-info">
            <span class="card-name">{{ villager.name_es }}</span>
            <span class="card-species">{{ villager.species }}</span>
          </div>
        </article>
      </li>
    </ul>
  </section>
</template>

<style scoped>
.marquee-container {
    width: 100%;
    overflow: hidden;
    padding: 1.5rem 0;
    background: linear-gradient(135deg, #fef9e7 0%, #fdf2e9 100%);
    border-top: 3px solid #662028;
    border-bottom: 3px solid #662028;
}

.marquee-track {
    display: flex;
    gap: 1.5rem;
    width: max-content;
    animation: marquee 30s linear infinite;
    list-style: none;
    margin: 0;
    padding: 0;
}

.marquee-container:hover .marquee-track,
.marquee-container:focus-within .marquee-track {
    animation-play-state: paused;
}

/* Focus visible para navegación por teclado */
.marquee-container:focus-visible {
    outline: 3px solid var(--ac-gold-bright, #fbbc0a);
    outline-offset: 4px;
}

.villager-card {
    flex-shrink: 0;
    width: 160px;
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    cursor: default;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.villager-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.card-image-wrapper {
    width: 100%;
    height: 140px;
    background: linear-gradient(145deg, #d5f5e3 0%, #abebc6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
}

.card-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.card-info {
    padding: 0.75rem;
    text-align: center;
}

.card-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #5d4e37;
    margin: 0 0 0.25rem 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-species {
    font-size: 0.8rem;
    color: #8b7355;
    margin: 0;
}

@keyframes marquee {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

/* Responsive: más lento en móvil para mejor legibilidad */
@media (max-width: 768px) {
    .villager-card {
        width: 130px;
    }

    .card-image-wrapper {
        height: 110px;
    }

    .marquee-track {
        animation-duration: 25s;
    }
}
</style>
