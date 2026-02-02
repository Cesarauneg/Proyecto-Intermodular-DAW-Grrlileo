<template>
  <section class="max-w-7xl mx-auto px-4 py-6" aria-labelledby="villagers-title">
    <header class="text-center mb-8">
      <h2 id="villagers-title" class="text-4xl font-extrabold">Catálogo de Aldeanos</h2>
      <p class="text-gray-500 mt-2">
        Explora todos los personajes y descubre sus personalidades
      </p>
    </header>

    <!-- BUSCADOR -->
    <SearchBar v-model="filters.search" placeholder="Buscar aldeano..." @update:modelValue="resetPage">
      <div class="flex flex-col">
        <label for="filter-personality" class="sr-only">Filtrar por personalidad</label>
        <select
          id="filter-personality"
          v-model="filters.personality"
          @change="resetPage"
          class="text-xs rounded-lg border-gray-200 focus:ring-2 focus:ring-green-400 focus:border-transparent"
          aria-label="Filtrar por personalidad"
        >
          <option value="">Personalidad</option>
          <option v-for="p in filterOptions.personalities" :key="p" :value="p">{{ p }}</option>
        </select>
      </div>

      <div class="flex flex-col">
        <label for="filter-species" class="sr-only">Filtrar por especie</label>
        <select
          id="filter-species"
          v-model="filters.species"
          @change="resetPage"
          class="text-xs rounded-lg border-gray-200 focus:ring-2 focus:ring-green-400 focus:border-transparent"
          aria-label="Filtrar por especie"
        >
          <option value="">Especie</option>
          <option v-for="s in filterOptions.species" :key="s" :value="s">{{ s }}</option>
        </select>
      </div>

      <div class="flex flex-col">
        <label for="filter-gender" class="sr-only">Filtrar por género</label>
        <select
          id="filter-gender"
          v-model="filters.gender"
          @change="resetPage"
          class="text-xs rounded-lg border-gray-200 focus:ring-2 focus:ring-green-400 focus:border-transparent"
          aria-label="Filtrar por género"
        >
          <option value="">Género</option>
          <option v-for="g in filterOptions.genders" :key="g" :value="g">{{ g }}</option>
        </select>
      </div>
    </SearchBar>

    <!-- LOADING -->
    <div
      v-if="loading && !characters.length"
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
      aria-busy="true"
      aria-label="Cargando aldeanos"
    >
      <div v-for="i in 12" :key="i" class="h-64 bg-gray-100 animate-pulse rounded-xl"></div>
    </div>

    <!-- GRID -->
    <ul
      v-else-if="characters.length"
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
      role="list"
      aria-label="Lista de aldeanos"
    >
      <li v-for="char in characters" :key="char.id">
        <CharacterCard
          :character="char"
          :is-authenticated="isAuthenticated"
          @select="openModal"
        />
      </li>
    </ul>

    <!-- SIN RESULTADOS -->
    <p v-else class="text-center py-20 text-gray-400" role="status">
      No se encontraron resultados.
    </p>

    <!-- PAGINACIÓN -->
    <Pagination
      :current-page="filters.page"
      :total-pages="totalPages"
      @change="changePage"
    />

    <!-- MODAL -->
    <CharacterModal
      v-if="selectedCharacter"
      :character="selectedCharacter"
      @close="closeModal"
    />
  </section>
</template>

<script setup>
/**
 * @fileoverview Sección de catálogo de aldeanos/vecinos de Animal Crossing.
 * Muestra un grid de tarjetas con búsqueda y filtros.
 * Los datos se obtienen de /api/villagers con paginación.
 */

import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useFetch } from '@/Composables/useFetch'
import axios from 'axios'

import CharacterCard from '@/Components/CharacterCard.vue'
import CharacterModal from '@/Components/CharacterModal.vue'
import SearchBar from '@/Components/SearchBar.vue'
import Pagination from '@/Components/Pagination.vue'

/**
 * @typedef {Object} FilterOptions
 * @property {string[]} personalities - Lista de personalidades disponibles
 * @property {string[]} species - Lista de especies disponibles
 * @property {string[]} genders - Lista de géneros disponibles
 * @property {string[]} hobbies - Lista de hobbies disponibles
 */

/**
 * @typedef {Object} Villager
 * @property {number} id - ID único del aldeano
 * @property {string} name_es - Nombre en español
 * @property {string} species - Especie del aldeano
 * @property {string} personality - Tipo de personalidad
 * @property {string} gender - Género del aldeano
 * @property {string} image - URL de la imagen
 * @property {string} catch_es - Frase característica en español
 * @property {string} birthday_string - Fecha de cumpleaños
 * @property {string} hobby - Hobby del aldeano
 * @property {string} saying - Frase del aldeano
 */

/** Usuario autenticado (si existe) */
const page = usePage()
const isAuthenticated = computed(() => !!page.props.auth?.user)

/** Estado reactivo de los filtros de búsqueda */
const filters = reactive({
  search: '',
  personality: '',
  species: '',
  gender: '',
  page: 1,
  per_page: 20
})

/**
 * Opciones de filtros cargadas desde /api/villagers/filters
 * @type {import('vue').Ref<FilterOptions>}
 */
const filterOptions = ref({
  personalities: [],
  species: [],
  genders: [],
  hobbies: []
})

// Cargar opciones de filtros al montar el componente
onMounted(async () => {
  try {
    const res = await axios.get('/api/villagers/filters')
    filterOptions.value = res.data
  } catch (e) {
    console.error('Error cargando filtros:', e)
  }
})

/**
 * URL reactiva que se reconstruye cuando cambian los filtros.
 * Usa /api/villagers/filter cuando hay filtros activos, sino /api/villagers.
 */
const url = computed(() => {
  const hasFilters = filters.search || filters.personality || filters.species || filters.gender
  const endpoint = hasFilters ? '/api/villagers/filter' : '/api/villagers'

  const params = new URLSearchParams({
    page: filters.page,
    per_page: filters.per_page
  })

  if (filters.search) params.append('search', filters.search)
  if (filters.personality) params.append('personality', filters.personality)
  if (filters.species) params.append('species', filters.species)
  if (filters.gender) params.append('gender', filters.gender)

  return `${endpoint}?${params.toString()}`
})

// Fetch de datos con debounce de 500ms
const { data: pagination, loading } = useFetch(url, { debounce: 500 })

/** Lista de aldeanos de la página actual */
const characters = computed(() => pagination.value?.data || [])

/** Total de páginas disponibles */
const totalPages = computed(() => pagination.value?.last_page || 1)

/** Aldeano seleccionado para mostrar en el modal */
const selectedCharacter = ref(null)

/**
 * Abre el modal con los detalles del aldeano
 * @param {Villager} character - Aldeano seleccionado
 */
const openModal = (character) => {
  selectedCharacter.value = character
  document.body.style.overflow = 'hidden'
}

/** Cierra el modal y restaura el scroll */
const closeModal = () => {
  selectedCharacter.value = null
  document.body.style.overflow = ''
}

/** Resetea a la primera página (usado al cambiar filtros) */
const resetPage = () => {
  filters.page = 1
}

/**
 * Cambia a una página específica
 * @param {number} p - Número de página
 */
const changePage = (p) => {
  filters.page = p
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Limpiar overflow al desmontar
onUnmounted(() => {
  document.body.style.overflow = ''
})
</script>
