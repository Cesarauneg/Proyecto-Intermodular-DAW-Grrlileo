<script setup>
/**
 * AudioPlayer.vue
 *
 * Componente de reproductor de audio para música horaria.
 * Diseñado para usarse con el composable useHourlyMusic.
 *
 * Los datos de las canciones provienen de la tabla `hourly_music` de la BD,
 * pasados vía Inertia props y gestionados por useHourlyMusic.
 *
 * @example
 * <AudioPlayer
 *   :current-song="currentSong"
 *   :is-playing="isPlaying"
 *   :is-loading="isLoading"
 *   :volume="volume"
 *   @toggle-play="togglePlay"
 *   @next-song="nextSong"
 *   @prev-song="prevSong"
 *   @update:volume="setVolume"
 * />
 */
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import { useClickOutside } from '@/Composables/useClickOutside.js';

const props = defineProps({
  /** Canción actualmente seleccionada */
  currentSong: {
    type: Object,
    default: null,
    validator: (song) => !song || (song.id && song.titulo && song.autor)
  },
  /** Estado de reproducción */
  isPlaying: {
    type: Boolean,
    default: false
  },
  /** Indica si el audio está cargando */
  isLoading: {
    type: Boolean,
    default: false
  },
  /** Volumen actual (0-1) */
  volume: {
    type: Number,
    default: 0.5,
    validator: (v) => v >= 0 && v <= 1
  }
});

const emit = defineEmits([
  'toggle-play',
  'next-song',
  'prev-song',
  'update:volume',
  'update:isPlaying'
]);

const titleRef = ref(null);
const volumeContainerRef = ref(null);
const isOverflowing = ref(false);
const showVolumeSlider = ref(false);

// Cerrar slider de volumen al hacer clic fuera
useClickOutside(volumeContainerRef, () => {
  showVolumeSlider.value = false;
});

/** Título de la canción actual o placeholder */
const displayTitle = computed(() => props.currentSong?.titulo ?? 'Sin música');

/** Autor de la canción actual */
const displayAuthor = computed(() => props.currentSong?.autor ?? '—');

/** Texto para lectores de pantalla sobre el estado actual */
const statusAnnouncement = computed(() => {
  if (props.isLoading) return 'Cargando música';
  if (!props.currentSong) return 'No hay música disponible';
  return props.isPlaying
    ? `Reproduciendo: ${props.currentSong.titulo}`
    : `Pausado: ${props.currentSong.titulo}`;
});

/**
 * Verifica si el título desborda su contenedor para activar marquee.
 */
function checkOverflow() {
  nextTick(() => {
    if (titleRef.value) {
      isOverflowing.value = titleRef.value.scrollWidth > titleRef.value.parentElement.clientWidth;
    }
  });
}

watch(() => props.currentSong, checkOverflow);
onMounted(checkOverflow);

/**
 * Maneja el cambio de volumen desde el slider.
 * @param {Event} event - Evento del input range
 */
function handleVolumeChange(event) {
  const newVolume = parseFloat(event.target.value);
  emit('update:volume', newVolume);
}

/**
 * Alterna la visibilidad del control de volumen.
 */
function toggleVolumeSlider() {
  showVolumeSlider.value = !showVolumeSlider.value;
}

/**
 * Maneja el toggle de play/pause y emite el evento correspondiente.
 */
function handleTogglePlay() {
  emit('toggle-play');
  emit('update:isPlaying', !props.isPlaying);
}
</script>

<template>
  <article
    class="audio-player"
    role="region"
    aria-label="Reproductor de música horaria de Animal Crossing"
  >
    <!-- Estado para lectores de pantalla -->
    <div class="sr-only" aria-live="polite" aria-atomic="true">
      {{ statusAnnouncement }}
    </div>

    <!-- Información de la canción -->
    <figure class="song-info">
      <figcaption class="sr-only">Canción actual</figcaption>
      <p
        ref="titleRef"
        class="song-title"
        :class="{ 'marquee': isOverflowing && !isLoading }"
      >
        <span v-if="isLoading" class="loading-indicator">
          <span class="loading-dot"></span>
          <span class="loading-dot"></span>
          <span class="loading-dot"></span>
        </span>
        <span v-else>{{ displayTitle }}</span>
      </p>
      <p class="song-artist">{{ displayAuthor }}</p>
    </figure>

    <!-- Controles de reproducción -->
    <div class="controls" role="group" aria-label="Controles de reproducción">
      <!-- Botón anterior -->
      <button
        @click="emit('prev-song')"
        class="control-btn"
        type="button"
        aria-label="Canción anterior"
        :disabled="!currentSong"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M9.195 18.44c1.25.713 2.805-.19 2.805-1.629v-2.34l6.945 3.968c1.25.714 2.805-.188 2.805-1.628V8.688c0-1.44-1.555-2.342-2.805-1.628L12 11.03v-2.34c0-1.44-1.555-2.343-2.805-1.629l-7.108 4.062c-1.26.72-1.26 2.536 0 3.256l7.108 4.061z" />
        </svg>
      </button>

      <!-- Botón play/pause -->
      <button
        @click="handleTogglePlay"
        class="play-btn"
        :class="{ 'is-playing': isPlaying, 'is-loading': isLoading }"
        type="button"
        :aria-label="isPlaying ? 'Pausar reproducción' : 'Iniciar reproducción'"
        :aria-pressed="isPlaying"
        :disabled="!currentSong || isLoading"
      >
        <!-- Icono de carga -->
        <svg v-if="isLoading" class="spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
        </svg>
        <!-- Icono play -->
        <svg v-else-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd" />
        </svg>
        <!-- Icono pause -->
        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M6.75 5.25a.75.75 0 01.75-.75H9a.75.75 0 01.75.75v13.5a.75.75 0 01-.75.75H7.5a.75.75 0 01-.75-.75V5.25zm7.5 0A.75.75 0 0115 4.5h1.5a.75.75 0 01.75.75v13.5a.75.75 0 01-.75.75H15a.75.75 0 01-.75-.75V5.25z" clip-rule="evenodd" />
        </svg>
      </button>

      <!-- Botón siguiente -->
      <button
        @click="emit('next-song')"
        class="control-btn"
        type="button"
        aria-label="Siguiente canción"
        :disabled="!currentSong"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M5.055 7.06c-1.25-.714-2.805.189-2.805 1.628v8.123c0 1.44 1.555 2.342 2.805 1.628L12 14.471v2.34c0 1.44 1.555 2.342 2.805 1.628l7.108-4.061c1.26-.72 1.26-2.536 0-3.256L14.805 7.06C13.555 6.346 12 7.25 12 8.688v2.34L5.055 7.06z" />
        </svg>
      </button>

      <!-- Contenedor de volumen -->
      <div ref="volumeContainerRef" class="volume-wrapper">
        <button
          @click="toggleVolumeSlider"
          class="control-btn volume-btn"
          type="button"
          :aria-label="`Volumen: ${Math.round(volume * 100)}%`"
          :aria-expanded="showVolumeSlider"
        >
          <svg v-if="volume === 0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M13.5 4.06c0-1.336-1.616-2.005-2.56-1.06l-4.5 4.5H4.508c-1.141 0-2.318.664-2.66 1.905A9.76 9.76 0 001.5 12c0 .898.121 1.768.35 2.595.341 1.24 1.518 1.905 2.659 1.905h1.93l4.5 4.5c.945.945 2.561.276 2.561-1.06V4.06zM17.78 9.22a.75.75 0 10-1.06 1.06L18.44 12l-1.72 1.72a.75.75 0 001.06 1.06l1.72-1.72 1.72 1.72a.75.75 0 101.06-1.06L20.56 12l1.72-1.72a.75.75 0 00-1.06-1.06l-1.72 1.72-1.72-1.72z" />
          </svg>
          <svg v-else-if="volume < 0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M13.5 4.06c0-1.336-1.616-2.005-2.56-1.06l-4.5 4.5H4.508c-1.141 0-2.318.664-2.66 1.905A9.76 9.76 0 001.5 12c0 .898.121 1.768.35 2.595.341 1.24 1.518 1.905 2.659 1.905h1.93l4.5 4.5c.945.945 2.561.276 2.561-1.06V4.06zM18.584 5.106a.75.75 0 011.06 0c3.808 3.807 3.808 9.98 0 13.788a.75.75 0 01-1.06-1.06 8.25 8.25 0 000-11.668.75.75 0 010-1.06z" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M13.5 4.06c0-1.336-1.616-2.005-2.56-1.06l-4.5 4.5H4.508c-1.141 0-2.318.664-2.66 1.905A9.76 9.76 0 001.5 12c0 .898.121 1.768.35 2.595.341 1.24 1.518 1.905 2.659 1.905h1.93l4.5 4.5c.945.945 2.561.276 2.561-1.06V4.06zM18.584 5.106a.75.75 0 011.06 0c3.808 3.807 3.808 9.98 0 13.788a.75.75 0 01-1.06-1.06 8.25 8.25 0 000-11.668.75.75 0 010-1.06z" />
            <path d="M15.932 7.757a.75.75 0 011.061 0 6 6 0 010 8.486.75.75 0 01-1.06-1.061 4.5 4.5 0 000-6.364.75.75 0 010-1.06z" />
          </svg>
        </button>

        <!-- Slider de volumen (desplegable) -->
        <Transition name="volume-slide">
          <div v-if="showVolumeSlider" class="volume-slider-container">
            <label for="volume-slider" class="sr-only">Ajustar volumen</label>
            <input
              id="volume-slider"
              type="range"
              min="0"
              max="1"
              step="0.05"
              :value="volume"
              @input="handleVolumeChange"
              class="volume-slider"
              aria-valuemin="0"
              aria-valuemax="100"
              :aria-valuenow="Math.round(volume * 100)"
              aria-valuetext="`${Math.round(volume * 100)}% volumen`"
            />
          </div>
        </Transition>
      </div>
    </div>
  </article>
</template>

<style scoped>
/* ---- Audio Player Container ---- */
.audio-player {
  background: var(--ac-player-bg);
  backdrop-filter: blur(12px);
  border-radius: 16px;
  padding: 12px 16px;
  border: 1px solid var(--ac-player-border);
  box-shadow: 0 4px 20px var(--ac-player-shadow);
  display: flex;
  align-items: center;
  gap: 16px;
  width: 280px;
  position: relative;
}

/* ---- Song Information ---- */
.song-info {
  flex: 1;
  min-width: 0;
  overflow: hidden;
  margin: 0;
}

.song-title {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--ac-player-text);
  margin: 0;
  white-space: nowrap;
  display: inline-block;
}

.song-title.marquee {
  animation: marquee 8s linear infinite;
  padding-right: 2rem;
}

.song-info:hover .song-title.marquee {
  animation-play-state: paused;
}

@keyframes marquee {
  0% { transform: translateX(0%); }
  100% { transform: translateX(-100%); }
}

.song-artist {
  font-size: 0.7rem;
  color: var(--ac-player-text-muted);
  margin: 2px 0 0 0;
}

/* ---- Loading Indicator ---- */
.loading-indicator {
  display: inline-flex;
  gap: 4px;
  align-items: center;
}

.loading-dot {
  width: 6px;
  height: 6px;
  background: var(--ac-player-text-muted);
  border-radius: 50%;
  animation: loading-bounce 1.4s ease-in-out infinite both;
}

.loading-dot:nth-child(1) { animation-delay: -0.32s; }
.loading-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes loading-bounce {
  0%, 80%, 100% {
    transform: scale(0.6);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

/* ---- Playback Controls ---- */
.controls {
  display: flex;
  align-items: center;
  gap: 6px;
}

.control-btn {
  background: transparent;
  border: none;
  width: 26px;
  height: 26px;
  padding: 4px;
  cursor: pointer;
  color: var(--ac-player-text-muted);
  transition: color 0.2s, opacity 0.2s;
}

.control-btn:hover:not(:disabled),
.control-btn:focus-visible {
  color: var(--ac-player-text);
}

.control-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.control-btn:focus-visible {
  outline: 2px solid var(--ac-gold-bright);
  outline-offset: 2px;
  border-radius: 4px;
}

.control-btn svg {
  width: 100%;
  height: 100%;
}

.play-btn {
  background: linear-gradient(135deg, var(--ac-player-btn-start), var(--ac-player-btn-end));
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  padding: 8px;
  cursor: pointer;
  color: var(--ac-player-text);
  transition: transform 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px var(--ac-player-btn-shadow);
}

.play-btn:hover:not(:disabled) {
  transform: scale(1.1);
  box-shadow: 0 4px 12px var(--ac-player-btn-shadow);
}

.play-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.play-btn:focus-visible {
  outline: 2px solid var(--ac-gold-bright);
  outline-offset: 2px;
}

.play-btn svg {
  width: 100%;
  height: 100%;
}

.play-btn.is-loading svg {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* ---- Volume Controls ---- */
.volume-wrapper {
  position: relative;
}

.volume-btn {
  margin-left: 2px;
}

.volume-slider-container {
  position: absolute;
  bottom: calc(100% + 8px);
  right: 8px;
  background: var(--ac-player-bg);
  backdrop-filter: blur(12px);
  border: 1px solid var(--ac-player-border);
  border-radius: 8px;
  padding: 8px 12px;
  box-shadow: 0 4px 12px var(--ac-player-shadow);
}

.volume-slider {
  width: 80px;
  height: 4px;
  appearance: none;
  background: var(--ac-player-border);
  border-radius: 2px;
  cursor: pointer;
}

.volume-slider::-webkit-slider-thumb {
  appearance: none;
  width: 14px;
  height: 14px;
  background: var(--ac-player-btn-start);
  border-radius: 50%;
  cursor: pointer;
  transition: transform 0.15s;
}

.volume-slider::-webkit-slider-thumb:hover {
  transform: scale(1.2);
}

.volume-slider::-moz-range-thumb {
  width: 14px;
  height: 14px;
  background: var(--ac-player-btn-start);
  border-radius: 50%;
  border: none;
  cursor: pointer;
}

/* ---- Volume Slider Transition ---- */
.volume-slide-enter-active,
.volume-slide-leave-active {
  transition: opacity 0.15s, transform 0.15s;
}

.volume-slide-enter-from,
.volume-slide-leave-to {
  opacity: 0;
  transform: translateY(4px);
}
</style>
