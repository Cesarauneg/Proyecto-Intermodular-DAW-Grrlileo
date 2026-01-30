<template>
  <div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Catálogo de Peces</h1>

    <SearchBar v-model="filters.search" @update:modelValue="resetPage">
      <select v-model="filters.location" @change="resetPage" class="text-xs rounded-lg border-gray-200">
        <option value="">Ubicación</option>
        <option value="Sea">Mar</option>
        <option value="River">Río</option>
        <option value="Pond">Estanque</option>
      </select>

      <select v-model="filters.rarity" @change="resetPage" class="text-xs rounded-lg border-gray-200">
        <option value="">Rareza</option>
        <option value="Common">Común</option>
        <option value="Uncommon">Poco común</option>
        <option value="Rare">Raro</option>
        <option value="Ultra-rare">Ultra raro</option>
      </select>
    </SearchBar>

    <div v-if="loading && !fishes.length" class="grid grid-cols-2 md:grid-cols-6 gap-3">
      <div v-for="i in 12" :key="i" class="h-32 bg-gray-100 animate-pulse rounded-lg"></div>
    </div>

    <div v-else-if="fishes.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
      <FishCard v-for="fish in fishes" :key="fish.id" :fish="fish" />
    </div>

    <div v-else class="text-center py-20 text-gray-400">
      No se encontraron resultados.
    </div>

    <Pagination 
      :current-page="filters.page" 
      :total-pages="totalPages" 
      @change="changePage" 
    />
  </div>
</template>

<script setup>
import { reactive, computed, watch } from 'vue'
import { useFetch } from '@/Composables/useFetch'
import FishCard from '@/Components/FishCard.vue'
import SearchBar from '@/Components/SearchBar.vue'
import Pagination from '@/Components/Pagination.vue'

// 1. Configuración de Filtros y Estado
const filters = reactive({
  search: '',
  location: '',
  rarity: '',
  page: 1,
  per_page: 18
})

// 2. Construcción de la URL (Reactiva)
const url = computed(() => {
  const hasFilters = filters.search || filters.location || filters.rarity;
  const endpoint = hasFilters ? '/api/fish/filter' : '/api/fish';
  
  const params = new URLSearchParams({
    page: filters.page,
    per_page: filters.per_page
  });

  if (filters.search) params.append('search', filters.search);
  if (filters.location) params.append('location', filters.location);
  if (filters.rarity) params.append('rarity', filters.rarity);

  return `${endpoint}?${params.toString()}`;
})

// 3. Fetch de datos usando el Composable con Debounce
const { data: pagination, loading } = useFetch(url, { debounce: 500 })

// 4. Propiedades Computadas para el Template
const fishes = computed(() => pagination.value?.data || [])
const totalPages = computed(() => pagination.value?.last_page || 1)

// 5. Funciones de Navegación
const resetPage = () => { 
  filters.page = 1 
}

const changePage = (p) => {
  filters.page = p
  // Hacemos scroll arriba al cambiar de página
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>