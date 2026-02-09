import { ref, computed, watch, onMounted, onUnmounted } from 'vue'

/**
 * @typedef {Object} HourlySong
 * @property {number} id - ID único de la canción en la base de datos
 * @property {string} titulo - Título formateado (ej: "14:00 - Soleado")
 * @property {string} autor - Autor de la canción (siempre "Totakeke")
 * @property {string} weather - Clima original (Sunny, Rainy, Snowy)
 * @property {string} src - URL del archivo de audio
 */

/**
 * @typedef {Object.<number, HourlySong[]>} HourlyMusicData
 * Objeto con las canciones agrupadas por hora (0-23).
 * Cada hora contiene un array de 3 canciones (una por clima).
 * Datos provenientes de la tabla `hourly_music` vía Inertia props.
 */

/**
 * Composable para gestionar la reproducción de música por hora.
 *
 * La música proviene de la base de datos `hourly_music`:
 * - 24 horas × 3 climas = 72 canciones totales
 * - El composable detecta la hora local del usuario
 * - Selecciona aleatoriamente una de las 3 variantes de clima
 * - Actualiza la lista cuando cambia la hora
 *
 * @param {HourlyMusicData} hourlyMusicData - Canciones agrupadas por hora (de Inertia props)
 * @returns {Object} Estado y métodos para controlar el reproductor
 */
export function useHourlyMusic(hourlyMusicData) {
  /** @type {import('vue').Ref<HTMLAudioElement|null>} Elemento de audio HTML5 */
  const audioElement = ref(null)

  /** @type {import('vue').Ref<boolean>} Estado de reproducción */
  const isPlaying = ref(false)

  /** @type {import('vue').Ref<number>} Índice de la canción actual (0-2) */
  const currentSongIndex = ref(0)

  /** @type {import('vue').Ref<number>} Hora actual del usuario (0-23) */
  const currentHour = ref(new Date().getHours())

  /** @type {import('vue').Ref<number>} Volumen del audio (0-1) */
  const volume = ref(0.5)

  /** @type {import('vue').Ref<boolean>} Indica si el usuario ha interactuado con la página */
  const userHasInteracted = ref(false)

  /** @type {import('vue').Ref<boolean>} Indica si el audio está cargando */
  const isLoading = ref(false)

  /** @type {number|null} ID del intervalo para verificar cambio de hora */
  let hourCheckInterval = null

  /**
   * Canciones disponibles para la hora actual.
   * @type {import('vue').ComputedRef<HourlySong[]>}
   */
  const currentHourSongs = computed(() => {
    if (!hourlyMusicData || !hourlyMusicData[currentHour.value]) {
      return []
    }
    return hourlyMusicData[currentHour.value]
  })

  /**
   * Canción actualmente seleccionada.
   * @type {import('vue').ComputedRef<HourlySong|null>}
   */
  const currentSong = computed(() => {
    const songs = currentHourSongs.value
    if (!songs || songs.length === 0) return null
    return songs[currentSongIndex.value] || songs[0]
  })

  /**
   * Inicializa el elemento de audio HTML5.
   * @private
   */
  function initAudio() {
    if (audioElement.value) return

    audioElement.value = new Audio()
    audioElement.value.volume = volume.value
    audioElement.value.preload = 'metadata'

    // Eventos del audio
    audioElement.value.addEventListener('ended', handleSongEnded)
    audioElement.value.addEventListener('canplay', () => {
      isLoading.value = false
    })
    audioElement.value.addEventListener('waiting', () => {
      isLoading.value = true
    })
    audioElement.value.addEventListener('error', (e) => {
      console.error('[useHourlyMusic] Error de audio:', e)
      isLoading.value = false
    })
  }

  /**
   * Maneja el evento cuando una canción termina.
   * Pasa a la siguiente canción automáticamente.
   * @private
   */
  function handleSongEnded() {
    nextSong()
    if (isPlaying.value) {
      play()
    }
  }

  /**
   * Selecciona una canción aleatoria de las disponibles.
   * Se usa al iniciar y cuando cambia la hora.
   * @private
   */
  function selectRandomSong() {
    const songs = currentHourSongs.value
    if (songs && songs.length > 0) {
      currentSongIndex.value = Math.floor(Math.random() * songs.length)
    }
  }

  /**
   * Carga la canción actual en el elemento de audio.
   * @private
   */
  function loadCurrentSong() {
    if (!audioElement.value || !currentSong.value) return

    isLoading.value = true
    audioElement.value.src = currentSong.value.src
    audioElement.value.load()
  }

  /**
   * Inicia la reproducción de audio.
   * Requiere interacción previa del usuario (política de navegadores).
   */
  async function play() {
    if (!audioElement.value || !currentSong.value) return

    try {
      // Cargar si la fuente cambió
      if (audioElement.value.src !== currentSong.value.src) {
        loadCurrentSong()
      }

      await audioElement.value.play()
      isPlaying.value = true
      userHasInteracted.value = true
    } catch (error) {
      // Error común: el usuario no ha interactuado todavía
      if (error.name === 'NotAllowedError') {
        console.warn('[useHourlyMusic] Reproducción bloqueada. Esperando interacción del usuario.')
        isPlaying.value = false
      } else {
        console.error('[useHourlyMusic] Error al reproducir:', error)
      }
    }
  }

  /**
   * Pausa la reproducción de audio.
   */
  function pause() {
    if (!audioElement.value) return

    audioElement.value.pause()
    isPlaying.value = false
  }

  /**
   * Alterna entre play y pause.
   */
  function togglePlay() {
    if (isPlaying.value) {
      pause()
    } else {
      play()
    }
  }

  /**
   * Avanza a la siguiente canción de la hora actual.
   */
  function nextSong() {
    const songs = currentHourSongs.value
    if (!songs || songs.length === 0) return

    currentSongIndex.value = (currentSongIndex.value + 1) % songs.length
  }

  /**
   * Retrocede a la canción anterior de la hora actual.
   */
  function prevSong() {
    const songs = currentHourSongs.value
    if (!songs || songs.length === 0) return

    currentSongIndex.value = currentSongIndex.value === 0
      ? songs.length - 1
      : currentSongIndex.value - 1
  }

  /**
   * Actualiza el volumen del audio.
   * @param {number} newVolume - Nuevo volumen (0-1)
   */
  function setVolume(newVolume) {
    volume.value = Math.max(0, Math.min(1, newVolume))
    if (audioElement.value) {
      audioElement.value.volume = volume.value
    }
  }

  /**
   * Verifica si ha cambiado la hora y actualiza la lista.
   * @private
   */
  function checkHourChange() {
    const newHour = new Date().getHours()
    if (newHour !== currentHour.value) {
      currentHour.value = newHour
      selectRandomSong()

      // Si estaba reproduciendo, cargar y continuar con la nueva canción
      if (isPlaying.value) {
        loadCurrentSong()
        play()
      }
    }
  }

  /**
   * Registra la interacción del usuario para habilitar autoplay.
   * Llamar cuando el usuario haga clic en cualquier parte de la página.
   */
  function registerUserInteraction() {
    userHasInteracted.value = true
  }

  /**
   * Intenta iniciar la reproducción automática.
   * Solo funciona si el usuario ya ha interactuado con la página.
   */
  async function attemptAutoplay() {
    if (!audioElement.value || !currentSong.value) return

    loadCurrentSong()

    try {
      await audioElement.value.play()
      isPlaying.value = true
      userHasInteracted.value = true
    } catch {
      // Autoplay bloqueado - esperamos interacción del usuario
      isPlaying.value = false
    }
  }

  // Observar cambios en la canción actual para cargarla y reproducirla
  watch(currentSong, (newSong, oldSong) => {
    if (newSong && audioElement.value && oldSong) {
      const wasPlaying = isPlaying.value
      loadCurrentSong()
      if (wasPlaying) {
        play()
      }
    }
  })

  // Observar cambios en el volumen
  watch(volume, (newVolume) => {
    if (audioElement.value) {
      audioElement.value.volume = newVolume
    }
  })

  // Ciclo de vida
  onMounted(() => {
    initAudio()
    selectRandomSong()
    loadCurrentSong()

    // Verificar cambio de hora cada minuto
    hourCheckInterval = setInterval(checkHourChange, 60000)

    // Intentar autoplay
    attemptAutoplay()
  })

  onUnmounted(() => {
    if (hourCheckInterval) {
      clearInterval(hourCheckInterval)
    }

    if (audioElement.value) {
      audioElement.value.pause()
      audioElement.value.src = ''
      audioElement.value.removeEventListener('ended', handleSongEnded)
      audioElement.value = null
    }
  })

  return {
    // Estado
    isPlaying,
    isLoading,
    currentSong,
    currentHourSongs,
    currentHour,
    currentSongIndex,
    volume,
    userHasInteracted,

    // Métodos
    play,
    pause,
    togglePlay,
    nextSong,
    prevSong,
    setVolume,
    registerUserInteraction,
    attemptAutoplay,
  }
}
