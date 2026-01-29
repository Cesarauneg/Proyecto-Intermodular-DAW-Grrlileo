<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue';

const emit = defineEmits(['update:isPlaying']);

// Canciones hardcodeadas
const songs = [
    { id: 1, titulo: 'K.K. Salsa', autor: 'Totakeke' },
    { id: 2, titulo: 'K.K. Bossa', autor: 'Totakeke' },
    { id: 3, titulo: 'Bubblegum K.K. tu tut utut utu tut ut ututut chao', autor: 'Totakeke' },
];

const isPlaying = ref(false);
const currentSongIndex = ref(0);
const titleRef = ref(null);
const isOverflowing = ref(false);

const currentSong = computed(() => songs[currentSongIndex.value]);

function checkOverflow() {
    nextTick(() => {
        if (titleRef.value) {
            isOverflowing.value = titleRef.value.scrollWidth > titleRef.value.parentElement.clientWidth;
        }
    });
}

watch(currentSongIndex, checkOverflow);
onMounted(checkOverflow);

function togglePlay() {
    isPlaying.value = !isPlaying.value;
    emit('update:isPlaying', isPlaying.value);
}

function nextSong() {
    currentSongIndex.value = (currentSongIndex.value + 1) % songs.length;
}

function prevSong() {
    currentSongIndex.value = currentSongIndex.value === 0
        ? songs.length - 1
        : currentSongIndex.value - 1;
}
</script>

<template>
    <article class="audio-player" aria-label="Reproductor de música">
        <!-- Song info with live region for screen readers -->
        <figure class="song-info">
            <figcaption class="sr-only">Canción actual</figcaption>
            <p
                ref="titleRef"
                class="song-title"
                :class="{ 'marquee': isOverflowing }"
                aria-live="polite"
            >
                {{ currentSong.titulo }}
            </p>
            <p class="song-artist">{{ currentSong.autor }}</p>
        </figure>

        <!-- Playback controls -->
        <div class="controls" role="group" aria-label="Controles de reproducción">
            <button
                @click="prevSong"
                class="control-btn"
                type="button"
                aria-label="Canción anterior"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M9.195 18.44c1.25.713 2.805-.19 2.805-1.629v-2.34l6.945 3.968c1.25.714 2.805-.188 2.805-1.628V8.688c0-1.44-1.555-2.342-2.805-1.628L12 11.03v-2.34c0-1.44-1.555-2.343-2.805-1.629l-7.108 4.062c-1.26.72-1.26 2.536 0 3.256l7.108 4.061z" />
                </svg>
            </button>

            <button
                @click="togglePlay"
                class="play-btn"
                type="button"
                :aria-label="isPlaying ? 'Pausar reproducción' : 'Iniciar reproducción'"
                :aria-pressed="isPlaying"
            >
                <svg v-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M6.75 5.25a.75.75 0 01.75-.75H9a.75.75 0 01.75.75v13.5a.75.75 0 01-.75.75H7.5a.75.75 0 01-.75-.75V5.25zm7.5 0A.75.75 0 0115 4.5h1.5a.75.75 0 01.75.75v13.5a.75.75 0 01-.75.75H15a.75.75 0 01-.75-.75V5.25z" clip-rule="evenodd" />
                </svg>
            </button>

            <button
                @click="nextSong"
                class="control-btn"
                type="button"
                aria-label="Siguiente canción"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M5.055 7.06c-1.25-.714-2.805.189-2.805 1.628v8.123c0 1.44 1.555 2.342 2.805 1.628L12 14.471v2.34c0 1.44 1.555 2.342 2.805 1.628l7.108-4.061c1.26-.72 1.26-2.536 0-3.256L14.805 7.06C13.555 6.346 12 7.25 12 8.688v2.34L5.055 7.06z" />
                </svg>
            </button>
        </div>
    </article>
</template>

<style scoped>
/* ---- Audio Player Container ---- */
.audio-player {
    background: rgba(30, 30, 40, 0.85);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    padding: 12px 16px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    gap: 16px;
    width: 260px;
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
    color: #fff;
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
    color: rgba(255, 255, 255, 0.5);
    margin: 2px 0 0 0;
}

/* ---- Playback Controls ---- */
.controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.control-btn {
    background: transparent;
    border: none;
    width: 28px;
    height: 28px;
    padding: 4px;
    cursor: pointer;
    color: rgba(255, 255, 255, 0.7);
    transition: color 0.2s;
}

.control-btn:hover,
.control-btn:focus-visible {
    color: #fff;
}

.control-btn:focus-visible {
    outline: 2px solid var(--ac-gold-bright, #fbbc0a);
    outline-offset: 2px;
    border-radius: 4px;
}

.control-btn svg {
    width: 100%;
    height: 100%;
}

.play-btn {
    background: linear-gradient(135deg, #8bc34a, #689f38);
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    padding: 8px;
    cursor: pointer;
    color: #fff;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(139, 195, 74, 0.4);
}

.play-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(139, 195, 74, 0.5);
}

.play-btn:focus-visible {
    outline: 2px solid var(--ac-gold-bright, #fbbc0a);
    outline-offset: 2px;
}

.play-btn svg {
    width: 100%;
    height: 100%;
}

/* ---- Accessibility ---- */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
</style>
