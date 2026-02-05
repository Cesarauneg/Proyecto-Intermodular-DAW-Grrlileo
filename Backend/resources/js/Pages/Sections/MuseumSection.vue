<template>
    <section class="w-full animate-fade-in" aria-labelledby="museo-title">
        <!-- Header -->
        <header class="ac-section-header relative">
            <!-- Contador en esquina superior derecha (responsive) -->
            <div class="absolute top-2 right-4 bg-white px-3 py-1 md:px-5 md:py-2 lg:px-6 lg:py-3 rounded-xl border border-ac-border shadow-sm transition-all">
                <span class="text-[var(--ac-text-light)] text-[10px] md:text-xs lg:text-sm font-bold uppercase block">Total</span>
                <span class="text-sm md:text-xl lg:text-2xl font-black text-[var(--ac-green-dark)]">{{ totalGeneral }}</span>
            </div>

            <h2 id="museo-title" class="ac-section-title">
                Mi Museo
            </h2>
            <p class="ac-section-subtitle">
                Tu colección personal de hallazgos y residentes
            </p>
        </header>

        <!-- Botón móvil para ver categorías (oculto si se controla desde header) -->
        <div v-if="!props.activeSubSection" class="flex justify-center mb-4 lg:hidden">
            <BackButton
                :label="currentCategory.name"
                :icon="currentCategory.icon"
                variant="amber"
                aria-label="Abrir menú de categorías"
                @click="isMobileMenuOpen = !isMobileMenuOpen"
            />
        </div>

        <!-- Nav de categorías (oculto si se controla desde header) -->
        <nav
            v-if="!props.activeSubSection"
            :class="[
                'justify-center mb-6 px-4',
                isMobileMenuOpen ? 'flex' : 'hidden lg:flex'
            ]"
            role="tablist"
            aria-label="Categorías del museo"
        >
            <div class="inline-flex flex-wrap justify-center gap-2 rounded-xl bg-ac-nav-cream p-2 shadow-[0_4px_0_theme(colors.ac.nav-brown-shadow)] border-3 border-ac-nav-brown">
                <button
                    v-for="category in categories"
                    :key="category.id"
                    role="tab"
                    :aria-selected="activeCategory === category.id"
                    :class="[
                        'px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
                        'focus:outline-none focus:ring-2 focus:ring-ac-nav-brown focus:ring-offset-2',
                        activeCategory === category.id
                            ? 'bg-ac-nav-brown text-white shadow-md'
                            : 'text-ac-nav-brown hover:bg-ac-nav-brown/10'
                    ]"
                    @click="selectCategory(category.id)"
                >
                    <span class="mr-1" aria-hidden="true">{{ category.icon }}</span>
                    {{ category.name }}
                    <span
                        v-if="category.count > 0"
                        class="ml-2 px-2 py-0.5 rounded-full text-xs"
                        :class="activeCategory === category.id ? 'bg-white/20' : 'bg-ac-nav-brown/10'"
                    >
                        {{ category.count }}
                    </span>
                </button>
            </div>
        </nav>

        <div class="relative min-h-[400px]">
            <div v-if="initialLoading" class="flex flex-col items-center justify-center py-20">
                <div class="w-16 h-16 border-4 border-[var(--ac-green-light)] border-t-[var(--ac-green)] rounded-full animate-spin"></div>
                <p class="mt-4 font-bold text-[var(--ac-text-light)]">Consultando los archivos de Sócrates...</p>
            </div>

            <div v-else-if="!currentItems || currentItems.length === 0" 
                class="ac-card text-center py-20 bg-white/50 border-dashed border-4">
                <div class="text-7xl mb-4 grayscale opacity-50">{{ currentCategory.emptyIcon }}</div>
                <h3 class="text-xl font-black text-[var(--ac-text-light)] mb-2">Galería aún sin inaugurar</h3>
                <p class="text-[var(--ac-text-light)] opacity-70">No has registrado ningún {{ currentCategory.singular }} todavía.</p>
            </div>

            <div v-else class="px-1 sm:px-2 md:px-4">
                <TransitionGroup
                    name="grid-fade"
                    tag="div"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-6 pb-4"
                >
                    <article v-for="item in displayedItems" :key="item.id"
                        @click="selectedItem = item"
                        class="overflow-hidden group transition-all duration-300 cursor-pointer bg-white rounded-lg sm:rounded-xl border border-gray-200 hover:border-green-400 shadow-sm hover:shadow-md"
                        style="padding: 0; max-width: 100%;"
                    >
                        <div class="aspect-square p-1 sm:p-2 md:p-3 bg-[var(--ac-bg-primary)] flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity bg-gradient-to-tr from-[var(--ac-green-light)]/20 to-transparent"></div>
                            <img :src="item.icon || item.image"
                                :alt="item.name_es"
                                class="w-full h-full object-contain drop-shadow-sm transform group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                                decoding="async" />
                        </div>

                        <div class="p-1 sm:p-1.5 md:p-2 text-center border-t border-gray-200">
                            <h3 class="font-bold text-[9px] sm:text-[10px] md:text-xs text-gray-800 truncate uppercase tracking-tight leading-tight">
                                {{ capitalize(item.name_es || item.name_en || item.name) }}
                            </h3>

                            <div v-if="activeCategory === 'villagers'" class="mt-0.5">
                                <span class="text-[7px] sm:text-[8px] bg-blue-100 text-blue-800 px-1 py-0.5 rounded-full font-bold">
                                    {{ item.personality }}
                                </span>
                            </div>
                            <div v-else class="flex items-center justify-center gap-0.5 mt-0.5 text-yellow-600">
                                <span class="text-[8px] sm:text-[9px] font-bold">💰 {{ item.price || item.buy_price || 0 }}</span>
                            </div>
                        </div>
                    </article>
                </TransitionGroup>
                <!-- Paginación -->
                <Pagination
                    v-if="totalPages > 1"
                    class="mt-6"
                    :current-page="currentPage"
                    :total-pages="totalPages"
                    @change="handlePageChange"
                />
            </div>
        </div>

        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="selectedItem"
                    class="fixed inset-0 bg-[var(--ac-text-primary)]/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                    @click="selectedItem = null"
                >
                    <div class="relative max-w-2xl w-full" @click.stop>
                        <CharacterModal v-if="activeCategory === 'villagers'"
                            :character="selectedItem"
                            @close="selectedItem = null" />

                        <CritterDetail v-else
                            :critter="selectedItem"
                            :is-available="false"
                            :is-in-museum="true"
                            :museum-icon-url="'/icons/museum.png'"
                            :show-museum-button="false"
                            :is-museum-mode="true" />
                    </div>
                </div>
            </Transition>
        </Teleport>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { capitalize } from '@/Utils/formatters.js'
import CritterDetail from '@/Components/CritterDetail.vue'
import CharacterModal from '@/Components/CharacterModal.vue'
import Pagination from '@/Components/Pagination.vue'
import BackButton from '@/Components/Base/BackButton.vue'

// --- PROPS ---
const props = defineProps({
  activeSubSection: {
    type: String,
    default: null
  }
})

// --- ESTADO ---
const activeCategory = ref(props.activeSubSection || 'bugs')
const selectedItem = ref(null)
const initialLoading = ref(true)
const isMobileMenuOpen = ref(false)

// --- PAGINACIÓN ---
const ITEMS_PER_PAGE = 12
const currentPage = ref(1)

// --- CATEGORÍAS ---
const categories = ref([
    { id: 'bugs', name: 'Bichos', icon: '🦋', emptyIcon: '🦋', singular: 'bicho', endpoint: '/user/bugs', count: 0, items: null },
    { id: 'fish', name: 'Peces', icon: '🐟', emptyIcon: '🐟', singular: 'pez', endpoint: '/user/fish', count: 0, items: null },
    { id: 'fossils', name: 'Fósiles', icon: '🦴', emptyIcon: '🦴', singular: 'fósil', endpoint: '/user/fossils', count: 0, items: null },
    { id: 'sea_creatures', name: 'Mar', icon: '🦑', emptyIcon: '🦑', singular: 'criatura marina', endpoint: '/user/sea_creatures', count: 0, items: null },
    { id: 'art', name: 'Arte', icon: '🎨', emptyIcon: '🎨', singular: 'obra de arte', endpoint: '/user/art', count: 0, items: null },
    { id: 'villagers', name: 'Vecinos', icon: '🏠', emptyIcon: '🏠', singular: 'vecino', endpoint: '/user/villagers', count: 0, items: null }
])

// Sincronizar con prop externa
watch(() => props.activeSubSection, (newVal) => {
  if (newVal && categories.value.some(c => c.id === newVal)) {
    activeCategory.value = newVal
    currentPage.value = 1
  }
})

// --- CARGAR TODOS LOS DATOS AL INICIO ---
const loadCategoryData = async (cat) => {
    try {
        const response = await fetch(cat.endpoint)
        const data = await response.json()
        cat.items = data
        cat.count = Array.isArray(data) ? data.length : 0
    } catch (e) {
        console.error(`Error al cargar ${cat.name}:`, e)
        cat.items = []
        cat.count = 0
    }
}

onMounted(async () => {
    // Cargar todas las categorías en paralelo
    await Promise.all(categories.value.map(cat => loadCategoryData(cat)))
    initialLoading.value = false
})

// --- SELECCIÓN DE CATEGORÍA ---
const selectCategory = (categoryId) => {
    activeCategory.value = categoryId
    currentPage.value = 1
    isMobileMenuOpen.value = false
}

// --- COMPUTADOS ---
const currentCategory = computed(() => categories.value.find(c => c.id === activeCategory.value))
const currentItems = computed(() => currentCategory.value?.items || [])
const totalGeneral = computed(() => categories.value.reduce((acc, c) => acc + (c.count || 0), 0))

// Items paginados
const displayedItems = computed(() => {
    const start = (currentPage.value - 1) * ITEMS_PER_PAGE
    const end = start + ITEMS_PER_PAGE
    return currentItems.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(currentItems.value.length / ITEMS_PER_PAGE))

// --- MÉTODOS ---
const handlePageChange = (page) => {
    currentPage.value = page
}
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Transición para el modal */
.fade-enter-active, .fade-leave-active { transition: all 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: scale(0.95); }

/* Transición para el grid de items */
.grid-fade-enter-active, 
.grid-fade-leave-active { 
    transition: all 0.3s ease; 
}
.grid-fade-enter-from, 
.grid-fade-leave-to { 
    opacity: 0; 
    transform: translateY(10px); 
}
</style>

