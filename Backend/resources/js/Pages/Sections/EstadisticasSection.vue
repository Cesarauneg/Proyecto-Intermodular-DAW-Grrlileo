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
    <header class="text-center py-6 px-4">
      <h2 id="estadisticas-title" class="text-4xl font-extrabold text-green-800">
        Estadísticas
      </h2>
      <p class="text-gray-500 mt-2">
        Sigue así, Sócrates está muy orgulloso de tus hallazgos
      </p>
    </header>

    <MuseumStats v-if="stats && maximos" :stats="stats" :maximos="maximos" />
  </section>
</template>
