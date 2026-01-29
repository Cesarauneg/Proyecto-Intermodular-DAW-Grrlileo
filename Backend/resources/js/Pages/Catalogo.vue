<template>
  <div class="max-w-7xl mx-auto px-4 py-6">
    <div class="text-center mb-8">
      <h1 class="text-4xl font-extrabold">Catálogo de Aldeanos</h1>
        <p class="text-gray-500 mt-2">
      Explora todos los personajes y descubre sus personalidades
    </p>
  </div>

    <!-- GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <CharacterCard
        v-for="char in characters"
        :key="char.id"
        :character="char"
        @select="openModal"
      />
    </div>

    <!-- PAGINACIÓN -->
    <div class="flex justify-center mt-6 space-x-2">
      <button
        @click="prevPage"
        :disabled="page <= 1"
        class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
      >
        « Anterior
      </button>

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

      <button
        @click="nextPage"
        :disabled="!hasMore"
        class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
      >
        Siguiente »
      </button>
    </div>

    <!-- MODAL -->
    <CharacterModal
      v-if="selectedCharacter"
      :character="selectedCharacter"
      @close="selectedCharacter = null"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'

import CharacterCard from '@/Components/CharacterCard.vue'
import CharacterModal from '@/Components/CharacterModal.vue'

const characters = ref([])
const page = ref(1)
const perPage = 20
const totalPages = ref(1)
const hasMore = ref(true)

const selectedCharacter = ref(null)

// Abrir modal
const openModal = (character) => {
  selectedCharacter.value = character
}

// Cargar personajes
const loadCharacters = async () => {
  const res = await axios.get(`/api/villagers?page=${page.value}&per_page=${perPage}`)

  characters.value = res.data.data
  totalPages.value = res.data.last_page
  hasMore.value = page.value < totalPages.value
}

// Paginación
const nextPage = () => {
  if (hasMore.value) page.value++
}

const prevPage = () => {
  if (page.value > 1) page.value--
}

const goToPage = (p) => {
  page.value = p
}

const visiblePages = computed(() => {
  const range = 2
  const start = Math.max(1, page.value - range)
  const end = Math.min(totalPages.value, page.value + range)

  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

watch(page, loadCharacters)
onMounted(loadCharacters)
</script>
