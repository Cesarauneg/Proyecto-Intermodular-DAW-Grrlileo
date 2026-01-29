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

    <div v-if="totalPages > 1" class="mt-10 flex flex-col items-center border-t pt-6 gap-4">
      <div class="flex items-center space-x-2">
        <button 
          @click="changePage(filters.page - 1)" 
          :disabled="filters.page <= 1"
          class="px-4 py-2 bg-white border rounded-lg shadow-sm disabled:opacity-30 hover:bg-gray-50 transition-colors"
        >
          &laquo; Ant.
        </button>

        <div class="flex items-center px-4 py-2 bg-gray-50 rounded-lg border border-gray-100">
          <span class="text-sm font-semibold text-blue-600">{{ filters.page }}</span>
          <span class="text-sm text-gray-400 mx-2">/</span>
          <span class="text-sm text-gray-600">{{ totalPages }}</span>
        </div>

        <button 
          @click="changePage(filters.page + 1)" 
          :disabled="filters.page >= totalPages"
          class="px-4 py-2 bg-white border rounded-lg shadow-sm disabled:opacity-30 hover:bg-gray-50 transition-colors"
        >
          Sig. &raquo;
        </button>
      </div>
      
      <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">
        Total de páginas: {{ totalPages }}
      </p>
    </div>
    
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { useFetch } from '@/Composables/useFetch'
import FishCard from '@/Components/FishCard.vue'
import SearchBar from '@/Components/SearchBar.vue'

const filters = reactive({
  search: '',
  location: '',
  rarity: '',
  page: 1,
  per_page: 8
})

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

const { data: pagination, loading } = useFetch(url, { debounce: 500 })

const fishes = computed(() => pagination.value?.data || [])
const totalPages = computed(() => pagination.value?.last_page || 1)

// Funciones de navegación
const resetPage = () => { 
  filters.page = 1 
}

const changePage = (p) => {
  filters.page = p
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>