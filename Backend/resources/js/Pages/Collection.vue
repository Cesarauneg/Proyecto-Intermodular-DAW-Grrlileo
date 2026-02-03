<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-purple-50">

        <!-- Header -->
        <div class="bg-white/80 backdrop-blur border-b-4 border-green-300 shadow-lg sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <h1 class="text-4xl sm:text-5xl font-bold text-center text-green-800 mb-2">
                    🏛️ Mi Museo
                </h1>
                <p class="text-center text-gray-600">
                    Tu colección personal de Animal Crossing
                </p>
            </div>

            <!-- Tabs de categorías -->
            <div class="max-w-5xl mx-auto px-4 pb-4">
                <div class="flex gap-2 overflow-x-auto pb-2">
                    <button v-for="category in categories" :key="category.id" @click="activeCategory = category.id"
                        :class="[
              'px-4 py-2 rounded-full font-semibold whitespace-nowrap transition-all duration-200 flex items-center gap-2',
              activeCategory === category.id
                ? 'bg-gradient-to-r from-green-400 to-blue-400 text-white shadow-lg scale-105'
                : 'bg-white text-gray-600 hover:bg-gray-100 border-2 border-gray-200'
            ]">
                        <span class="text-xl">{{ category.icon }}</span>
                        <span>{{ category.name }}</span>
                        <span v-if="category.count !== null" :class="[
                'px-2 py-0.5 rounded-full text-xs font-bold',
                activeCategory === category.id ? 'bg-white/30' : 'bg-gray-200'
              ]">
                            {{ category.count }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenido de la categoría activa -->
        <div class="max-w-7xl mx-auto px-4 py-8">

            <!-- Loading -->
            <div v-if="loading" class="text-center py-20">
                <div
                    class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-green-400 border-t-transparent">
                </div>
                <p class="mt-4 text-gray-600">Cargando colección...</p>
            </div>

            <!-- Vacío -->
            <div v-else-if="!currentItems || currentItems.length === 0" class="text-center py-20">
                <div class="text-8xl mb-4">{{ currentCategory.emptyIcon }}</div>
                <h3 class="text-2xl font-bold text-gray-400 mb-2">Colección vacía</h3>
                <p class="text-gray-500">Aún no has agregado ningún {{ currentCategory.singular }} a tu museo</p>
            </div>

            <!-- Grid de items -->
            <ul v-else
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-6"
                role="list" :aria-label="`Lista de ${currentCategory.name.toLowerCase()}`">

                <li v-for="item in currentItems" :key="item.id">
                    <article @click="selectedItem = item"
                        class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 cursor-pointer overflow-hidden border-2 border-gray-200 hover:border-green-400">
                        <!-- Imagen -->
                        <div
                            class="aspect-square p-4 bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center">
                            <img :src="item.icon || item.image"
                                :alt="capitalize(item.name_es || item.name_en || item.name)"
                                class="w-full h-full object-contain drop-shadow-lg" />
                        </div>

                        <!-- Info -->
                        <div class="p-3 bg-white border-t-2 border-gray-100">
                            <h3 class="font-bold text-sm sm:text-base text-center text-gray-800 truncate mb-1">
                                {{ capitalize(item.name_es || item.name_en || item.name) }}
                            </h3>

                            <!-- Para vecinos mostrar personalidad, para otros el precio -->
                            <div v-if="activeCategory === 'villagers'" class="text-center">
                                <p class="text-xs text-gray-500">{{ item.personality }}</p>
                            </div>
                            <div v-else class="flex items-center justify-center gap-2 text-green-700">
                                <span class="text-lg font-bold">💰</span>
                                <span class="font-semibold text-sm">{{ item.price || item.buy_price || 0 }}</span>
                            </div>
                        </div>
                    </article>
                </li>
            </ul>

        </div>

        <!-- Modal de detalles -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="selectedItem">

                    <CharacterModal v-if="activeCategory === 'villagers'" :character="selectedItem"
                        @close="selectedItem = null" />

                    <div v-else @click="selectedItem = null"
                        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                        <div @click.stop class="max-w-2xl w-full">
                            <CritterDetail :critter="selectedItem" :is-available="false" :is-in-museum="true"
                                :museum-icon-url="'/icons/museum.png'" :show-museum-button="false"
                                :is-museum-mode="true" />
                        </div>
                    </div>

                </div>
            </Transition>
        </Teleport>

    </div>
</template>

<script setup>
    import {
        ref,
        computed,
        watch
    } from 'vue'
    import {
        useFetch
    } from '@/Composables/useFetch.js'
    import {
        capitalize
    } from '@/Utils/formatters.js'
    import CritterDetail from '@/Components/CritterDetail.vue'
    import CharacterModal from '@/Components/CharacterModal.vue'

    const activeCategory = ref('bugs')
    const selectedItem = ref(null)
    const loading = ref(false)

    const characters = computed(() => pagination.value ?.data || [])

    // Definir categorías
    const categories = ref([{
            id: 'bugs',
            name: 'Bichos',
            icon: '🦋',
            emptyIcon: '🦋',
            singular: 'bicho',
            endpoint: '/user/bugs',
            count: null,
            items: null
        },
        {
            id: 'fish',
            name: 'Peces',
            icon: '🐟',
            emptyIcon: '🐟',
            singular: 'pez',
            endpoint: '/user/fish',
            count: null,
            items: null
        },
        {
            id: 'fossils',
            name: 'Fósiles',
            icon: '🦴',
            emptyIcon: '🦴',
            singular: 'fósil',
            endpoint: '/user/fossils',
            count: null,
            items: null
        },
        {
            id: 'sea_creatures',
            name: 'Criaturas Marinas',
            icon: '🦑',
            emptyIcon: '🦑',
            singular: 'criatura marina',
            endpoint: '/user/sea_creatures',
            count: null,
            items: null
        },
        {
            id: 'art',
            name: 'Arte',
            icon: '🎨',
            emptyIcon: '🎨',
            singular: 'obra de arte',
            endpoint: '/user/art',
            count: null,
            items: null
        },
        {
            id: 'villagers',
            name: 'Vecinos',
            icon: '🏠',
            emptyIcon: '🏠',
            singular: 'vecino',
            endpoint: '/user/villagers',
            count: null,
            items: null
        }
    ])

    // Cargar datos de la categoría activa
    watch(activeCategory, async (newCategory) => {
        const category = categories.value.find(c => c.id === newCategory)

        // Si ya tenemos los datos, no hacer fetch de nuevo
        if (category.items !== null) {
            return
        }

        loading.value = true

        try {
            const {
                data
            } = useFetch(category.endpoint)

            watch(data, (newData) => {
                if (newData) {
                    category.items = newData
                    category.count = Array.isArray(newData) ? newData.length : 0
                }
            }, {
                immediate: true
            })
        } catch (error) {
            console.error('Error cargando colección:', error)
        } finally {
            loading.value = false
        }
    }, {
        immediate: true
    })

    // Categoria y items actuales
    const currentCategory = computed(() => {
        return categories.value.find(c => c.id === activeCategory.value)
    })

    const currentItems = computed(() => {
        return currentCategory.value ?.items || []
    })

</script>

<style scoped>
    .modal-enter-active,
    .modal-leave-active {
        transition: opacity 0.3s ease;
    }

    .modal-enter-from,
    .modal-leave-to {
        opacity: 0;
    }

</style>
