import { ref, watch } from 'vue'
import { useFetch } from '@/Composables/useFetch.js'
import axios from 'axios'

const favoriteIds = ref(new Set())

export function useFavorites() {
    const { data } = useFetch('/user/villagers')

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
            await axios.post(`/villagers/${numericId}/favorite`)
        } catch (err) {
            // Rollback si falla
            console.error("Error al guardar favorito", err)
            reload() // O revertir manualmente
        }
    }

    return { isFavorite, toggleFavorite }
}