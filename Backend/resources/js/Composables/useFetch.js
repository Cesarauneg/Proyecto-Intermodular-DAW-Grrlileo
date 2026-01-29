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
    } catch (e) {
      error.value = e
    } finally {
      loading.value = false
    }
  }

  // ESTO ES VITAL: Si la url es un computed, hay que vigilarla
  if (isRef(url)) {
    watch(url, () => {
      if (options.debounce > 0) {
        clearTimeout(timeoutId)
        timeoutId = setTimeout(fetchData, options.debounce)
      } else {
        fetchData()
      }
    })
  }

  fetchData() // Carga inicial
  return { data, loading, error }
}