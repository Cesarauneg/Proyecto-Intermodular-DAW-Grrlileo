import { ref, watch } from 'vue'
import { useFetch } from '@/Composables/useFetch.js'
import { API } from '@/Utils/api.js'
import axios from 'axios'

const favoriteIds = ref(new Set())

export function useFavorites() {
    const { data } = useFetch(API.user.villagers)

    watch(data, (newData) => {
        if (Array.isArray(newData)) {
            favoriteIds.value = new Set(newData.map(v => Number(v.id)))
        }
    }, { immediate: true })

    const isFavorite = (id) => favoriteIds.value.has(Number(id))

    const toggleFavorite = async (id) => {
        const numericId = Number(id)

        if (favoriteIds.value.has(numericId)) {
            favoriteIds.value.delete(numericId)
        } else {
            favoriteIds.value.add(numericId)
        }
        favoriteIds.value = new Set(favoriteIds.value)

        try {
            await axios.post(API.favorite(numericId))
        } catch (err) {
            if (favoriteIds.value.has(numericId)) favoriteIds.value.delete(numericId)
            else favoriteIds.value.add(numericId)
            favoriteIds.value = new Set(favoriteIds.value)
            console.error("Error al guardar favorito", err)
        }
    }

    return { isFavorite, toggleFavorite }
}
