// Composables/useMuseum.js
import { ref, watch } from 'vue'
import { useFetch } from '@/Composables/useFetch.js'
import axios from 'axios'

const museumIds = ref(new Set())

export function useMuseum(apiUrl = '/user/bugs') {
    const { data: userData, reload } = useFetch(apiUrl)

    // Sincronizar el Set con los datos de la DB
    watch(userData, (newData) => {
        if (Array.isArray(newData)) {
            museumIds.value = new Set(newData.map(item => Number(item.id)))
        }
    }, { immediate: true })

    const isInMuseum = (id) => museumIds.value.has(Number(id))

    const toggleDonation = async (id, type = 'bugs') => {
        const numericId = Number(id)
        
        if (museumIds.value.has(numericId)) {
            museumIds.value.delete(numericId)
        } else {
            museumIds.value.add(numericId)
        }
        museumIds.value = new Set(museumIds.value) 

        try {
            await axios.post(`/${type}/${numericId}/donate`)
        } catch (err) {
            if (museumIds.value.has(numericId)) museumIds.value.delete(numericId)
            else museumIds.value.add(numericId)
            museumIds.value = new Set(museumIds.value)
            console.error("Error al sincronizar con el museo:", err)
        }
    }

    return {
        museumIds,
        isInMuseum,
        toggleDonation,
        reloadMuseum: reload
    }
}