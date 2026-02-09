<script setup>
import MuseumStats from '@/Components/MuseumStats.vue';

defineProps({
  stats: Object,
  maximos: Object
});
</script>

<template>
  <section class="w-full" aria-labelledby="estadisticas-title">
    <!-- Header -->
    <header class="ac-section-header">
      <h2 id="estadisticas-title" class="ac-section-title">
        Estadísticas
      </h2>
      <p class="ac-section-subtitle">
        Sigue así, Sócrates está muy orgulloso de tus hallazgos
      </p>
    </header>

    <MuseumStats v-if="stats && maximos" :stats="stats" :maximos="maximos" />
  </section>
</template>


