<template>
  <div v-if="totalPages > 1" class="flex justify-center mt-6 space-x-2">
    <button
      @click="emitPage(currentPage - 1)"
      :disabled="currentPage <= 1"
      class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
    >
      « Anterior
    </button>

    <button
      v-for="p in visiblePages"
      :key="p"
      @click="emitPage(p)"
      :class="[
        'px-3 py-1 rounded',
        currentPage === p ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'
      ]"
    >
      {{ p }}
    </button>

    <button
      @click="emitPage(currentPage + 1)"
      :disabled="currentPage >= totalPages"
      class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
    >
      Siguiente »
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
  }
}

const visiblePages = computed(() => {
  const range = 2
  const start = Math.max(1, props.currentPage - range)
  const end = Math.min(props.totalPages, props.currentPage + range)

  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})
</script>