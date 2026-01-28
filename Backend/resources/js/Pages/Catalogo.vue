<template>
  <div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6 text-center">Catálogo</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <CharacterCard
        v-for="char in characters"
        :key="char.id"
        :character="char"
      />
    </div>

    <!-- Paginación -->
    <div class="flex justify-center mt-6 space-x-2">
      <!-- Botón Anterior -->
      <button
        @click="prevPage"
        :disabled="page <= 1"
        class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
      >
        &laquo; Anterior
      </button>

      <!-- Números de página -->
      <button
        v-for="p in visiblePages"
        :key="p"
        @click="goToPage(p)"
        :class="[
          'px-3 py-1 rounded',
          page === p ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'
        ]"
      >
        {{ p }}
      </button>

      <!-- Botón Siguiente -->
      <button
        @click="nextPage"
        :disabled="!hasMore"
        class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
      >
        Siguiente &raquo;
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import CharacterCard from '@/Components/CharacterCard.vue'

const characters = ref([])
const page = ref(1)
const perPage = 20
const hasMore = ref(true)
const totalPages = ref(1) // total de páginas, lo sacaremos del backend si quieres

// Cargar personajes
const loadCharacters = async () => {
  const res = await axios.get(`/api/villagers?page=${page.value}&per_page=${perPage}`)

  // Respuesta de Laravel paginate:
  characters.value = res.data.data       // personajes de la página
  totalPages.value = res.data.last_page  // cantidad real de páginas
  hasMore.value = page.value < totalPages.value
}

// Funciones de paginación
const nextPage = () => {
  if (hasMore.value) page.value++
}

const prevPage = () => {
  if (page.value > 1) page.value--
}

const goToPage = (p) => {
  page.value = p
}

// Generar array de páginas visibles
const visiblePages = computed(() => {
  const pages = []
  const range = 2 // mostrar 2 páginas a cada lado de la actual
  const start = Math.max(1, page.value - range)
  const end = Math.min(totalPages.value, page.value + range) // nunca más que la última página

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

watch(page, loadCharacters)
onMounted(loadCharacters)
</script>
