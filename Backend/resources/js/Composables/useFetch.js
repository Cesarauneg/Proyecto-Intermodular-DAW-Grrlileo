// useFetch.js
import { ref, watch, isRef } from 'vue'
import axios from 'axios'

export function useFetch(url, options = { debounce: 0 }) {
    const data = ref(null)
    const loading = ref(true)
    const error = ref(null)
    let timeoutId = null

    const fetchData = async () => {
        const urlValue = isRef(url) ? url.value : url
        if (!urlValue) return

        loading.value = true
        
        try {
            const res = await axios.get(urlValue)
            data.value = res.data
            error.value = null
        } catch (e) {
            error.value = e
        } finally {
            loading.value = false
        }
    }

    // Lógica para vigilar cambios
    if (isRef(url)) {
        watch(url, () => {
            // Si hay un debounce configurado, esperamos
            if (options.debounce > 0) {
                clearTimeout(timeoutId)
                timeoutId = setTimeout(fetchData, options.debounce)
            } else {
                fetchData()
            }
        })
    }

    // Carga inicial
    fetchData()

    return { data, loading, error, reload: fetchData }
}