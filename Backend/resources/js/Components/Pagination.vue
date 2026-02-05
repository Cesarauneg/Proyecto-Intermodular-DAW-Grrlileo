<template>
  <div v-if="totalPages > 1" class="flex justify-center items-center gap-2 mt-8 pb-4">
    <button
      @click="emitPage(currentPage - 1)"
      :disabled="currentPage <= 1"
      class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border-2 border-ac-border text-ac-text-secondary hover:border-ac-green hover:text-ac-green disabled:opacity-30 disabled:cursor-not-allowed transition-all"
    >
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
    </button>

    <div class="flex items-center gap-1 bg-white/50 p-1 rounded-2xl border-2 border-[var(--ac-bg-secondary)]">
      <button
        v-for="p in visiblePages"
        :key="p"
        @click="emitPage(p)"
        :class="[
          'w-10 h-10 rounded-xl font-black transition-all',
          currentPage === p 
            ? 'bg-[var(--ac-green)] text-white shadow-sm scale-105' 
            : 'text-[var(--ac-text-light)] hover:bg-[var(--ac-bg-primary)]'
        ]"
      >
        {{ p }}
      </button>
    </div>

    <button
      @click="emitPage(currentPage + 1)"
      :disabled="currentPage >= totalPages"
      class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border-2 border-ac-border text-ac-text-secondary hover:border-ac-green hover:text-ac-green disabled:opacity-30 disabled:cursor-not-allowed transition-all"
    >
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: { type: Number, required: true },
  totalPages: { type: Number, required: true }
})

const emit = defineEmits(['change'])

const emitPage = (p) => {
  if (p >= 1 && p <= props.totalPages) {
    emit('change', p)
    // Scroll suave hacia arriba al cambiar de página
    window.scrollTo({ top: 200, behavior: 'smooth' });
  }
}

const visiblePages = computed(() => {
  const range = 2
  const start = Math.max(1, props.currentPage - range)
  const end = Math.min(props.totalPages, props.currentPage + range)
  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})
</script>