<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import { useClickOutside } from '@/Composables/useClickOutside';
import { useHourlyMusic } from '@/Composables/useHourlyMusic';
import InicioSection from './Sections/InicioSection.vue';
import CritterpediaSection from './Sections/CritterpediaSection.vue';
import TemporadaSection from './Sections/TemporadaSection.vue';
import EstadisticasSection from './Sections/EstadisticasSection.vue';
import MuseumSection from './Sections/MuseumSection.vue';
import VecinosSection from './Sections/VecinosSection.vue';
import MemoryGame from './Games/MemoryGame.vue';
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

// ========== MOBILE NAV ==========
const isMobileNavOpen = ref(false);
const mobileNavRef = ref(null);

useClickOutside(mobileNavRef, () => isMobileNavOpen.value = false);

function selectTab(key) {
  // Si es un item con dropdown, seleccionar el primer hijo automáticamente
  const item = navItems.value.find(i => i.key === key);
  if (item?.hasDropdown && item.children?.length > 0) {
    const firstChild = item.children[0];
    activeTab.value = firstChild.section;
    activeSubSection.value = firstChild.key;
  } else {
    activeTab.value = key;
    activeSubSection.value = null;
  }
  isMobileNavOpen.value = false;
  activeDropdown.value = null;
}

const activeDropdown = ref(null);
const dropdownCloseTimeout = ref(null);

function selectSubSection(child) {
  if (child.section === 'memory') {
    activeTab.value = 'memory';
    activeSubSection.value = child.key;
    loadMemoryLevel(child.level);
    isMobileNavOpen.value = false;
    activeDropdown.value = null;
    clearDropdownTimeout();
    return;
  }
  activeTab.value = child.section;
  activeSubSection.value = child.key;
  isMobileNavOpen.value = false;
  activeDropdown.value = null;
  clearDropdownTimeout();
}

function toggleDropdown(key) {
  activeDropdown.value = activeDropdown.value === key ? null : key;
}

function handleDropdownClick(item) {
  // Toggle del dropdown (tanto en móvil como en desktop)
  activeDropdown.value = activeDropdown.value === item.key ? null : item.key;
}

function clearDropdownTimeout() {
  if (dropdownCloseTimeout.value) {
    clearTimeout(dropdownCloseTimeout.value);
    dropdownCloseTimeout.value = null;
  }
}

function handleDropdownEnter(itemKey) {
  clearDropdownTimeout();
  activeDropdown.value = itemKey;
}

function handleDropdownLeave() {
  clearDropdownTimeout();
  dropdownCloseTimeout.value = setTimeout(() => {
    activeDropdown.value = null;
  }, 300); // 300ms delay antes de cerrar
}

// ========== TABS NAVIGATION ==========
const navItems = computed(() => {
    const items = [
        { key: 'inicio', label: 'Inicio' },
        {
            key: 'critterpedia',
            label: 'Critterpedia',
            hasDropdown: true,
            children: [
                { key: 'bichos', label: 'Bichos', icon: '🦋', section: 'critterpedia' },
                { key: 'peces', label: 'Peces', icon: '🐟', section: 'critterpedia' },
                { key: 'marinos', label: 'Criaturas marinas', icon: '🦑', section: 'critterpedia' },
                { key: 'fosiles', label: 'Fósiles', icon: '🦴', section: 'critterpedia' },
                { key: 'arte', label: 'Arte', icon: '🎨', section: 'critterpedia' },
            ]
        },
        {
            key: 'temporada',
            label: 'Temporada',
            hasDropdown: true,
            children: [
                { key: 'all', label: 'Todos', icon: '🌿', section: 'temporada' },
                { key: 'fish', label: 'Peces', icon: '🐟', section: 'temporada' },
                { key: 'bugs', label: 'Bichos', icon: '🦋', section: 'temporada' },
                { key: 'sea', label: 'Criaturas marinas', icon: '🦑', section: 'temporada' },
            ]
        },
        { key: 'vecinos', label: 'Vecinos' },
    ];

    if (user.value) {
        items.push({ key: 'estadisticas', label: 'Estadísticas' });
        items.push({
            key: 'museo',
            label: 'Mi museo',
            hasDropdown: true,
            children: [
                { key: 'bugs', label: 'Bichos', icon: '🦋', section: 'museo', count: props.stats?.bichos, max: props.maximos?.bichos },
                { key: 'fish', label: 'Peces', icon: '🐟', section: 'museo', count: props.stats?.peces, max: props.maximos?.peces },
                { key: 'fossils', label: 'Fósiles', icon: '🦴', section: 'museo', count: props.stats?.fosiles, max: props.maximos?.fosiles },
                { key: 'sea_creatures', label: 'Mar', icon: '🦑', section: 'museo', count: props.stats?.criaturas, max: props.maximos?.criaturas },
                { key: 'art', label: 'Arte', icon: '🎨', section: 'museo', count: props.stats?.arte, max: props.maximos?.arte },
                { key: 'villagers', label: 'Vecinos', icon: '🏠', section: 'museo' },
            ]
        });
        items.push({
            key: 'juegos',
            label: 'Juegos',
            hasDropdown: true,
            children: [1, 2, 3, 4, 5].map((level) => ({
                key: 'memory-' + level,
                label: 'Memory nivel ' + level,
                icon: '🧠',
                section: 'memory',
                level,
            })),
        });
    }
    return items;
});

const activeTab = ref('inicio');
const activeSubSection = ref(null);

//Si el usuario cierra sesión estando en estadísticas, lo mandamos a inicio
watch(user, (newUser) => {
    if (!newUser && (activeTab.value === 'estadisticas' || activeTab.value === 'memory')) {
        activeTab.value = 'inicio';
    }
});

// Recargar estadísticas cuando se cambia a la tab de estadísticas o museo
watch(activeTab, (newTab, oldTab) => {
    if ((newTab === 'estadisticas' || newTab === 'museo') && user.value) {
        // Solo recargar stats y maximos para no recargar todo
        router.reload({ only: ['stats', 'maximos'] });
    }
});

const sectionComponents = {
    inicio: InicioSection,
    critterpedia: CritterpediaSection,
    temporada: TemporadaSection,
    vecinos: VecinosSection,
    museo: MuseumSection,
    estadisticas: EstadisticasSection,
    memory: MemoryGame,

};

const memoryLevel = ref(1);
const memoryData = ref(null);
const memoryLoading = ref(false);
const memoryError = ref(null);

const loadMemoryLevel = async (level) => {
  if (!user.value) return;
  if (memoryData.value?.level === level && Array.isArray(memoryData.value?.cards) && memoryData.value.cards.length > 0) {
    memoryLevel.value = level;
    return;
  }
  memoryLevel.value = level;
  memoryLoading.value = true;
  memoryError.value = null;

  try {
    const response = await axios.get(route('memory-game.data', level));
    memoryData.value = response.data;
  } catch (error) {
    console.error('Error cargando el memory', error);
    memoryError.value = 'No se pudo cargar el memory.';
  } finally {
    memoryLoading.value = false;
  }
};

watch(
  user,
  (newUser) => {
    if (newUser) {
      loadMemoryLevel(1);
    }
  },
  { immediate: true }
);

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
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:p-4 focus:bg-white focus:text-ac-nav-brown focus:font-bold focus:rounded-lg focus:shadow-lg focus:top-2 focus:left-2">
      Saltar al contenido principal
    </a>
    <!-- ========== HEADER UNIFICADO ========== -->
    <header class="landing-header" ref="mobileNavRef">
      <div class="header-inner">
        <!-- Logo -->
        <div class="logo">
          <img
            src="/images/logos/logo.png"
            alt="Canela`s Desk"
            class="logo-img"
            width="140"
            height="80"
          />
        </div>

        <!-- Mobile Nav Trigger -->
        <button type="button" class="mobile-nav-trigger" @click.stop="isMobileNavOpen = !isMobileNavOpen">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
          </svg>
        </button>

        <!-- Nav Tabs -->
        <nav class="section-nav" :class="{ 'is-open': isMobileNavOpen }" role="navigation" aria-label="Navegación principal">
          <template v-for="item in navItems" :key="item.key">
            <!-- Item con dropdown -->
            <div
              v-if="item.hasDropdown"
              class="nav-item-dropdown"
              :class="{ 'is-active': activeDropdown === item.key }"
              @mouseenter="!isMobileNavOpen && handleDropdownEnter(item.key)"
              @mouseleave="!isMobileNavOpen && handleDropdownLeave()"
            >
              <button
                type="button"
                class="nav-tab has-dropdown"
                :class="{ active: activeTab === item.key }"
                @click.stop="handleDropdownClick(item)"
              >
                {{ item.label }}
                <svg class="dropdown-arrow" width="10" height="10" viewBox="0 0 10 10" fill="currentColor">
                  <path d="M2 3.5L5 6.5L8 3.5" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                </svg>
              </button>
              <ul class="nav-dropdown">
                <li v-for="child in item.children" :key="child.key">
                  <Link
                    v-if="child.href"
                    :href="child.href"
                    class="nav-dropdown-item"
                    role="menuitem"
                  >
                    <span class="dropdown-icon" aria-hidden="true">{{ child.icon }}</span>
                    {{ child.label }}
                  </Link>
                  <button
                    v-else
                    type="button"
                    class="nav-dropdown-item"
                    :class="{ active: activeTab === child.section && activeSubSection === child.key }"
                    @click.stop="selectSubSection(child)"
                  >
                    <span class="dropdown-icon" aria-hidden="true">{{ child.icon }}</span>
                    {{ child.label }}
                    <span
                      v-if="child.count !== undefined"
                      class="dropdown-count"
                      :class="{ 'dropdown-count--complete': child.count === child.max }"
                    >
                      {{ child.count }}/{{ child.max }}
                    </span>
                  </button>
                </li>
              </ul>
            </div>
            <!-- Item simple -->
            <button
              v-else
              type="button"
              class="nav-tab"
              :class="{ active: activeTab === item.key }"
              @click.stop="selectTab(item.key)"
            >
              {{ item.label }}
            </button>
          </template>
        </nav>

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
                  <Link :href="route('profile.edit')" class="dropdown-item" role="menuitem">Perfil</Link>
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


    <!-- ========== CONTENT ========== -->
    <main id="main-content" class="content-container" aria-live="polite">
      <Transition name="fade" mode="out-in">
        <section
          :key="activeTab"
          role="tabpanel"
          :id="`tabpanel-${activeTab}`"
          :aria-labelledby="`tab-${activeTab}`"
          class="tabpanel"
        >
          <div v-if="activeTab === 'memory'" class="space-y-4">
            <div v-if="memoryLoading" class="rounded-2xl border border-[color:var(--ac-border)] bg-white/80 p-6 text-center text-sm">
              Cargando memory...
            </div>
            <div v-else-if="memoryError" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-center text-sm text-red-600">
              {{ memoryError }}
            </div>
            <MemoryGame
              v-else
              embedded
              :level="memoryData?.level ?? memoryLevel"
              :pairs="memoryData?.pairs ?? 0"
              :cards="memoryData?.cards ?? []"
              :best-time="memoryData?.bestTime ?? null"
              :best-moves="memoryData?.bestMoves ?? null"
              :memorize-seconds="memoryData?.memorizeSeconds ?? 3"
            />
          </div>
          <component
            v-else
            :is="sectionComponents[activeTab]"
            :villagers="randomVillagers"
            :birthday-villagers="birthdayVillagers"
            :fish="props.fish"
            :bugs="props.bugs"
            :sea-creatures="props.seaCreatures"
            :stats="props.stats"
            :maximos="props.maximos"
            :active-sub-section="activeSubSection"
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
            <a href="https://github.com/Cesarauneg/Proyecto-Intermodular-DAW-Grrlileo" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="GitHub"><i class="fab fa-github"></i></a>
          </div>
        </div>

        <details class="footer-section footer-credits" open>
          <summary>Créditos</summary>
          <div class="footer-credits-body">
            <p>Desarrollado por estudiantes de DAW.</p>
            <p>Datos e imágenes cortesía de la comunidad.</p>
          </div>
        </details>
      </div>
      <div class="footer-bottom">
        <p>&copy; {{ new Date().getFullYear() }} Canela`s Desk. Todos los derechos reservados. Animal Crossing es una marca registrada de Nintendo. Esta aplicación no está afiliada ni respaldada por Nintendo. Todos los recursos, nombres e imágenes de personajes son propiedad de sus respectivos dueños.</p>
      </div>
    </footer>
  </div>
</template>

<style src="@/../css/pages/welcome.css"></style>




