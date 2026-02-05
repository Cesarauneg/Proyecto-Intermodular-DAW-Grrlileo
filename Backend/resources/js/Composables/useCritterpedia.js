/**
 * @fileoverview Composable reutilizable para todas las páginas *Pedia.
 * Centraliza la lógica de filtrado, selección, drawer y museo.
 */

import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useFetch } from '@/Composables/useFetch.js'
import { useMuseum } from '@/Composables/useMuseum.js'

/**
 * @param {Object} config - Configuración del composable
 * @param {string} config.apiEndpoint - Endpoint para obtener todos los items
 * @param {string|null} config.availableEndpoint - Endpoint para items disponibles (opcional)
 * @param {string} config.museumType - Tipo de colección ('bugs', 'fish', etc.)
 */
export function useCritterpedia(config) {
  const {
    apiEndpoint,
    availableEndpoint = null,
    museumType
  } = config

  // =============================================
  // ESTADO
  // =============================================
  const selectedItem = ref(null)
  const availableIds = ref(new Set())
  const drawerOpen = ref(false)

  // =============================================
  // AUTH
  // =============================================
  const page = usePage()
  const isAuthenticated = computed(() => !!page.props.auth?.user)

  // =============================================
  // MUSEO
  // =============================================
  const { isInMuseum, toggleDonation } = useMuseum(museumType)
  const museumIconUrl = '/icons/museum.png'

  // =============================================
  // FETCH DE DATOS
  // =============================================
  const { data: items } = useFetch(apiEndpoint)
  const { data: availableData } = availableEndpoint
    ? useFetch(availableEndpoint)
    : { data: ref(null) }

  // =============================================
  // WATCHERS
  // =============================================
  watch(availableData, (newData) => {
    if (Array.isArray(newData)) {
      availableIds.value = new Set(newData.map(item => item.id))
    }
  }, { immediate: true })

  // =============================================
  // FILTRADO
  // =============================================

  /**
   * Crea un computed con los items filtrados según los props
   * @param {Object} props - Props reactivos del componente
   * @param {Object} options - Opciones adicionales de filtrado
   */
  const createFilteredItems = (props, options = {}) => {
    const {
      additionalFilters = () => true,
      locationField = 'location',
      rarityField = 'rarity'
    } = options

    return computed(() => {
      if (!items.value) return []
      let result = [...items.value]

      // Búsqueda por nombre
      if (props.searchQuery) {
        const query = props.searchQuery.toLowerCase()
        result = result.filter(item =>
          item.name_es?.toLowerCase().includes(query) ||
          item.name?.toLowerCase().includes(query)
        )
      }

      // Disponibilidad
      if (props.filterAvailable === 'now') {
        result = result.filter(item => availableIds.value.has(item.id))
      } else if (props.filterAvailable === 'not') {
        result = result.filter(item => !availableIds.value.has(item.id))
      }

      // Ubicación
      if (props.filterLocation) {
        result = result.filter(item => item[locationField] === props.filterLocation)
      }

      // Rareza
      if (props.filterRarity) {
        result = result.filter(item => item[rarityField] === props.filterRarity)
      }

      // Precio
      if (props.filterPrice) {
        result = result.filter(item => {
          const price = item.price || 0
          const ranges = {
            low: [0, 1000],
            medium: [1000, 5000],
            high: [5000, 10000],
            premium: [10000, Infinity]
          }
          const [min, max] = ranges[props.filterPrice] || [0, Infinity]
          return price >= min && price < max
        })
      }

      // Velocidad (para criaturas marinas)
      if (props.filterSpeed) {
        result = result.filter(item => item.speed === props.filterSpeed)
      }

      // Falsificación (para arte)
      if (props.filterHasFake === 'yes') {
        result = result.filter(item => item.has_fake === true)
      } else if (props.filterHasFake === 'no') {
        result = result.filter(item => item.has_fake === false)
      }

      // Museo
      if (props.filterMuseum === 'donated') {
        result = result.filter(item => isInMuseum(item.id))
      } else if (props.filterMuseum === 'missing') {
        result = result.filter(item => !isInMuseum(item.id))
      }

      // Filtros adicionales personalizados
      result = result.filter(additionalFilters)

      return result
    })
  }

  /**
   * Limpia la selección si el item ya no está en la lista filtrada
   */
  const createSelectionWatcher = (filteredItems, filterProps) => {
    watch(filterProps, () => {
      if (selectedItem.value && !filteredItems.value.find(item => item.id === selectedItem.value.id)) {
        selectedItem.value = null
      }
    })
  }

  // =============================================
  // MÉTODOS
  // =============================================
  const selectItem = (item) => {
    selectedItem.value = item
    drawerOpen.value = false
  }

  const clearSelection = () => {
    selectedItem.value = null
  }

  const openDrawer = () => {
    drawerOpen.value = true
  }

  const closeDrawer = () => {
    drawerOpen.value = false
  }

  const handleToggle = (id) => {
    if (!isAuthenticated.value) return
    toggleDonation(id)
  }

  // =============================================
  // COMPUTED
  // =============================================
  const itemAvailability = computed(() =>
    selectedItem.value ? availableIds.value.has(selectedItem.value.id) : false
  )

  // =============================================
  // RETURN
  // =============================================
  return {
    // Estado
    items,
    selectedItem,
    availableIds,
    drawerOpen,
    isAuthenticated,
    museumIconUrl,

    // Métodos
    createFilteredItems,
    createSelectionWatcher,
    selectItem,
    clearSelection,
    openDrawer,
    closeDrawer,
    handleToggle,
    isInMuseum,

    // Computed
    itemAvailability
  }
}
