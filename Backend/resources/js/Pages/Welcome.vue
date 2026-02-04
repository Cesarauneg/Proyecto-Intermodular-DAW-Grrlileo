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
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3'; // Link, usePage are no longer needed here
import InicioSection from './Sections/InicioSection.vue';
import PecesSection from './Sections/PecesSection.vue';
import BichosSection from './Sections/BichosSection.vue';
import TemporadaSection from './Sections/TemporadaSection.vue';
import VecinosSection from './Sections/VecinosSection.vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue'; // NEW Import

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

// Removed page, user, authMenuOpen, authMenuRef, useClickOutside, toggleAuthMenu

// ========== TABS NAVIGATION ==========
const tabs = [
  { key: 'inicio', label: 'Inicio' },
  { key: 'peces', label: 'Peces' },
  { key: 'bichos', label: 'Bichos' },
  { key: 'temporada', label: 'Temporada' },
  { key: 'vecinos', label: 'Vecinos' },
];

const activeTab = ref('inicio');

const sectionComponents = {
  inicio: InicioSection,
  peces: PecesSection,
  bichos: BichosSection,
  temporada: TemporadaSection,
  vecinos: VecinosSection,
};

// Removed HOURLY MUSIC related logic (isPlaying, isLoading, etc.)
// Removed handlePageInteraction
</script>

<template>
  <DefaultLayout
    title="Inicio"
    :can-login="canLogin"
    :can-register="canRegister"
    :hourly-music="hourlyMusic"
  >
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
          />
        </section>
      </Transition>
    </main>
  </DefaultLayout>
</template>

<style scoped src="@/../css/pages/welcome.css"></style>