<script setup>
defineProps({
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
        <div class="marquee-track" aria-live="off">
            <!-- Primera copia de la lista -->
            <article
                v-for="villager in villagers"
                :key="'a-' + villager.id"
                class="villager-card"
                :aria-label="`${villager.name_es}, ${villager.species}`"
            >
                <div class="card-image-wrapper">
                    <img
                        :src="villager.image"
                        :alt="villager.name_es"
                        class="card-image"
                        loading="lazy"
                    />
                </div>
                <div class="card-info">
                    <h3 class="card-name">{{ villager.name_es }}</h3>
                    <p class="card-species">{{ villager.species }}</p>
                </div>
            </article>

            <!-- Segunda copia para loop infinito (decorativa) -->
            <article
                v-for="villager in villagers"
                :key="'b-' + villager.id"
                class="villager-card"
                aria-hidden="true"
            >
                <div class="card-image-wrapper">
                    <img
                        :src="villager.image"
                        alt=""
                        class="card-image"
                        loading="lazy"
                    />
                </div>
                <div class="card-info">
                    <h3 class="card-name">{{ villager.name_es }}</h3>
                    <p class="card-species">{{ villager.species }}</p>
                </div>
            </article>
        </div>
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
