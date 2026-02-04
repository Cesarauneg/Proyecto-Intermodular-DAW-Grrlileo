<script setup>
import MuseumStats from '@/Components/MuseumStats.vue';

defineProps({
  stats: Object,
  maximos: Object
});
</script>

<template>
  <div class="space-y-8">
    <div class="ac-card">
      <h2 class="text-xl font-bold text-[var(--ac-text-secondary)]">
        🏛️ Registro del Museo
      </h2>
      <p class="text-sm text-[var(--ac-text-light)] mt-1">
        Sigue así, Sócrates está muy orgulloso de tus hallazgos.
      </p>
    </div>

    <MuseumStats v-if="stats && maximos" :stats="stats" :maximos="maximos" />
  </div>
</template>
