<script setup>
import { ref, computed } from 'vue'
import { useFetch } from '@/Composables/useFetch'
import FishCard from '@/Components/FishCard.vue'

const search = ref('')
const page = ref(1)
const perPage = 18

// La URL ahora depende de la página Y del término de búsqueda
const url = computed(() => {
    // Si el usuario busca algo, solemos querer resetear a la página 1 en el backend,
    // pero aquí simplemente construimos la query string.
    let base = `/api/fish?page=${page.value}&per_page=${perPage}`
    if (search.value) {
        base += `&search=${encodeURIComponent(search.value)}`
    }
    return base
})

// Usamos el composable con un debounce de 500ms
const { data: pagination, loading } = useFetch(url, { debounce: 500 })

const fishes = computed(() => pagination.value?.data || [])
const totalPages = computed(() => pagination.value?.last_page || 1)

// Resetear página al buscar
const handleSearch = () => {
    page.value = 1
}
</script>

<template>
  <div class="max-w-6xl mx-auto px-4 py-8">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Critterpedia</h1>
      
      <div class="relative w-full md:w-64">
        <input 
          v-model="search" 
          @input="handleSearch"
          type="text" 
          placeholder="Buscar pez..." 
          class="w-full pl-10 pr-4 py-2 border rounded-full text-sm focus:ring-2 focus:ring-blue-400 outline-none transition-all"
        />
        <span class="absolute left-3 top-2.5 opacity-30">🔍</span>
      </div>
    </div>

    <div v-if="loading && !fishes.length" class="grid grid-cols-2 md:grid-cols-6 gap-3">
       <div v-for="i in 12" :key="i" class="h-40 bg-gray-100 animate-pulse rounded-lg"></div>
    </div>

    <div v-else-if="fishes.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
      <FishCard v-for="fish in fishes" :key="fish.id" :fish="fish" />
    </div>

    <div v-else class="text-center py-20 text-gray-400">
      No se encontraron especímenes que coincidan con "{{ search }}"
    </div>

    </div>
</template>