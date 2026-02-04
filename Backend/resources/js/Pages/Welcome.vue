<script setup>
/**
 * Welcome.vue - Página principal de Canela`s Desk
 *
 * Integra el sistema de música horaria:
 * - Recibe `hourlyMusic` desde Inertia (datos de tabla `hourly_music`)
 * - Usa composable `useHourlyMusic` para gestionar reproducción
 * - Sincroniza animación de Totakeke con estado de reproducción
 *
 * Estructura de hourlyMusic (desde backend):
 * {
 *   0: [{ id, titulo, autor, weather, src }, ...], // 3 canciones para las 00:00
 *   1: [{ id, titulo, autor, weather, src }, ...], // 3 canciones para las 01:00
 *   ...
 *   23: [...]
 * }
 */
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useClickOutside } from '@/Composables/useClickOutside';
import { useHourlyMusic } from '@/Composables/useHourlyMusic';
import InicioSection from './Sections/InicioSection.vue';
import PecesSection from './Sections/PecesSection.vue';
import BichosSection from './Sections/BichosSection.vue';
import TemporadaSection from './Sections/TemporadaSection.vue';
import EstadisticasSection from './Sections/EstadisticasSection.vue';
import VecinosSection from './Sections/VecinosSection.vue';
import AudioPlayer from '@/Components/AudioPlayer.vue';

const props = defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  randomVillagers: {
    type: Array,
    default: () => []
  },
  birthdayVillagers: {
    type: Array,
    default: () => []
  },
    fish: {
        type: Array,
        default: () => []
    },
    bugs: {
        type: Array,
        default: () => []
    },
    seaCreatures: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
          default: () => ({})
    },
    maximos: {
        type: Object,
        default: () => ({})
    },
  /**
   * Música horaria agrupada por hora (0-23).
   * Cada hora contiene 3 variantes de clima.
   * @type {Object.<number, Array<{id: number, titulo: string, autor: string, weather: string, src: string}>>}
   */
  hourlyMusic: {
    type: Object,
    default: () => ({})
  },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

// ========== AUTH MENU ==========
const authMenuOpen = ref(false);
const authMenuRef = ref(null);

useClickOutside(authMenuRef, () => authMenuOpen.value = false);

function toggleAuthMenu() {
  authMenuOpen.value = !authMenuOpen.value;
}

// ========== TABS NAVIGATION ==========
const tabs = computed(() => {
    const baseTabs = [
        { key: 'inicio', label: 'Inicio' },
        { key: 'peces', label: 'Peces' },
        { key: 'bichos', label: 'Bichos' },
        { key: 'temporada', label: 'Temporada' },
    ];

    // Solo añadimos Estadísticas si el usuario existe
    if (user.value) {
        baseTabs.push({ key: 'estadisticas', label: 'Estadísticas' });
    }

    baseTabs.push({ key: 'vecinos', label: 'Vecinos' });
    
    return baseTabs;
});

const activeTab = ref('inicio');

//Si el usuario cierra sesión estando en estadísticas, lo mandamos a inicio
import { watch } from 'vue';
watch(user, (newUser) => {
    if (!newUser && activeTab.value === 'estadisticas') {
        activeTab.value = 'inicio';
    }
});

const sectionComponents = {
  inicio: InicioSection,
  peces: PecesSection,
  bichos: BichosSection,
  temporada: TemporadaSection,
  estadisticas: EstadisticasSection,
  vecinos: VecinosSection,
};

// ========== HOURLY MUSIC ==========
/**
 * Composable para gestionar la música horaria.
 * Detecta la hora local del usuario y selecciona una canción aleatoria
 * de las 3 variantes disponibles (Sunny, Rainy, Snowy).
 */
const {
  isPlaying,
  isLoading,
  currentSong,
  currentHour,
  volume,
  togglePlay,
  nextSong,
  prevSong,
  setVolume,
  registerUserInteraction,
} = useHourlyMusic(props.hourlyMusic);

/**
 * Registra interacción del usuario al hacer clic en cualquier parte.
 * Necesario para habilitar autoplay en navegadores modernos.
 */
function handlePageInteraction() {
  registerUserInteraction();
}
</script>

<template>
    <Head title="Inicio" />

  <div class="landing" @click.once="handlePageInteraction">
    <!-- ========== HEADER ========== -->
    <header class="landing-header">
      <div class="header-inner">
        <!-- Logo -->
        <div class="logo">
          <img
            src="/images/logos/logo.png"
            alt="Canela`s Desk"
            class="logo-img"
            width="160"
            height="90"
          />
        </div>

        <!-- Auth -->
        <div class="auth-area" ref="authMenuRef">
          <button
            class="auth-trigger"
            @click.stop="toggleAuthMenu"
            :aria-expanded="authMenuOpen"
            aria-haspopup="true"
          >
            <template v-if="user">
              Hola, {{ user.name }}
            </template>
            <template v-else>
              Identifícate
            </template>
            <svg class="chevron" :class="{ open: authMenuOpen }" width="14" height="14" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </button>

          <Transition name="dropdown">
            <ul v-if="authMenuOpen" class="auth-dropdown" role="menu" aria-label="Menú de usuario">
              <!-- Guest menu -->
              <template v-if="!user">
                <li v-if="canLogin" role="none">
                  <Link :href="route('login')" class="dropdown-item" role="menuitem">Login</Link>
                </li>
                <li v-if="canRegister" role="none">
                  <Link :href="route('register')" class="dropdown-item" role="menuitem">Registro</Link>
                </li>
              </template>
              <!-- Authenticated menu -->
              <template v-else>
                  <li role="none">
                      <Link :href="route('dashboard')" class="dropdown-item" role="menuitem">Dashboard</Link>
                  </li>
                  <li role="none">
                      <Link :href="route('profile.edit')" class="dropdown-item" role="menuitem">Perfil</Link>
                  </li>
                  <li role="none">
                      <Link :href="route('catalogo')" class="dropdown-item" role="menuitem">Colección</Link>
                  </li>
                  <li role="none">
                      <Link :href="route('logout')" method="post" as="button" class="dropdown-item dropdown-item--danger" role="menuitem">Cerrar sesión</Link>
                  </li>
              </template>
            </ul>
          </Transition>
        </div>
      </div>
    </header>

    <!-- ========== NAV TABS ========== -->
    <nav class="section-nav" role="tablist" aria-label="Secciones del sitio">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        class="nav-tab"
        :class="{ active: activeTab === tab.key }"
        @click="activeTab = tab.key"
        role="tab"
        :aria-selected="activeTab === tab.key"
        :aria-controls="`tabpanel-${tab.key}`"
        :id="`tab-${tab.key}`"
      >
        {{ tab.label }}
      </button>
    </nav>

    <!-- ========== CONTENT ========== -->
    <main class="content-container" aria-live="polite">
      <Transition name="fade" mode="out-in">
        <section
          :key="activeTab"
          role="tabpanel"
          :id="`tabpanel-${activeTab}`"
          :aria-labelledby="`tab-${activeTab}`"
          class="tabpanel"
        >
          <component
            :is="sectionComponents[activeTab]"
            :villagers="randomVillagers"
            :birthday-villagers="birthdayVillagers"
            :fish="props.fish"
            :bugs="props.bugs"
            :sea-creatures="props.seaCreatures"
            :stats="props.stats"
            :maximos="props.maximos"
          />
        </section>
      </Transition>
    </main>

    <!-- ========== MASCOT + PLAYER (Semantic HTML5) ========== -->
    <aside
      class="mascot-area"
      role="complementary"
      aria-label="Reproductor de música y mascota"
    >
      <!--
        Figure semántico para la mascota Totakeke.
        La imagen se anima cuando la música está reproduciéndose.
      -->
      <figure class="mascot-figure">
        <img
          src="/images/logos/totakeke.gif"
          alt=""
          class="mascot-gif"
          :class="{
            'is-dancing': isPlaying,
            'is-paused': !isPlaying
          }"
          width="80"
          height="80"
          loading="lazy"
          aria-hidden="true"
        />
        <figcaption class="sr-only">
          Totakeke {{ isPlaying ? 'tocando música' : 'en espera' }}
        </figcaption>
      </figure>

      <!-- Indicador de hora actual -->
      <span class="hour-indicator" aria-label="Hora actual de la música">
        {{ String(currentHour).padStart(2, '0') }}:00
      </span>

      <!-- Reproductor de audio -->
      <AudioPlayer
        :current-song="currentSong"
        :is-playing="isPlaying"
        :is-loading="isLoading"
        :volume="volume"
        @toggle-play="togglePlay"
        @next-song="nextSong"
        @prev-song="prevSong"
        @update:volume="setVolume"
      />
    </aside>

    <!-- ========== FOOTER ========== -->
    <footer class="landing-footer">
      <div class="footer-content">
        <div class="footer-section">
          <h3>Canela`s Desk</h3>
          <p>Tu guía completa de Animal Crossing</p>
          <p>Contactanos:</p>
          <div class="social-links">
            <a href="mailto:info@canelasdesk.com" class="social-icon" aria-label="Email"><i class="far fa-envelope"></i></a>
            <a href="https://twitter.com/canelasdesk" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="https://instagram.com/canelasdesk" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://facebook.com/canelasdesk" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          </div>
        </div>

        <nav class="footer-section">
          <h3>Navegación</h3>
          <ul>
            <li><Link :href="route('welcome')" class="footer-link">Inicio</Link></li>
            <li><Link :href="route('dashboard')" class="footer-link">Dashboard</Link></li>
            <li><Link :href="route('catalogo')" class="footer-link">Colección</Link></li>
          </ul>
        </nav>

        <nav class="footer-section">
          <h3>Legal</h3>
          <ul>
            <li><a href="#" class="footer-link">Política de Privacidad</a></li>
            <li><a href="#" class="footer-link">Términos de Servicio</a></li>
            <li><a href="#" class="footer-link">Política de Cookies</a></li>
          </ul>
        </nav>
      </div>
      <div class="footer-bottom">
        <p>&copy; {{ new Date().getFullYear() }} Canela`s Desk. Todos los derechos reservados.</p>
      </div>
    </footer>
  </div>
</template>

<style scoped src="@/../css/pages/welcome.css"></style>
