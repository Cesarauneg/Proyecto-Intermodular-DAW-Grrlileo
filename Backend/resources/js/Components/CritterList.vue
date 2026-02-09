<!--
  @fileoverview Componente de lista lateral de criaturas.
  Muestra un grid de items seleccionables con scroll independiente.
-->
<template>
  <aside
    class="w-full lg:w-[30%] bg-white/80 backdrop-blur border-b-4 lg:border-b-0 lg:border-r-4 border-green-300 p-4 flex flex-col h-64 lg:h-full shadow-lg"
    :aria-label="`Lista de ${title}`"
  >
    <h2
      :id="listId"
      class="text-xl lg:text-2xl font-bold mb-4 text-green-800 text-center pb-2 border-b-2 border-green-300"
    >
      {{ title }}
      <span class="sr-only">({{ items.length }} elementos)</span>
    </h2>

    <!-- Scroll solo en la lista -->
    <nav
      class="flex-1 overflow-y-auto px-1"
      :aria-labelledby="listId"
      role="navigation"
    >
      <ul
        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 gap-3"
        role="listbox"
        :aria-label="`Selecciona un elemento de ${title}`"
        :aria-activedescendant="selectedItem ? `item-${selectedItem.id}` : undefined"
      >
        <li
          v-for="item in items"
          :key="item.id"
          :id="`item-${item.id}`"
          role="option"
          :aria-selected="selectedItem?.id === item.id"
        >
          <CritterGridItem
            :item="item"
            :is-selected="selectedItem?.id === item.id"
            :is-available="availableIds.has(item.id)"
            @select="$emit('select', item)"
          />
        </li>
      </ul>

      <!-- Estado vacío -->
      <p
        v-if="items.length === 0"
        class="text-center text-gray-400 py-8"
        role="status"
        aria-live="polite"
      >
        No se encontraron resultados
      </p>
    </nav>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import CritterGridItem from './CritterGridItem.vue'

const props = defineProps({
  /** Título de la lista */
  title: {
    type: String,
    default: 'Todos los elementos'
  },
  /** Array de criaturas a mostrar */
  items: {
    type: Array,
    default: () => []
  },
  /** Criatura actualmente seleccionada */
  selectedItem: {
    type: Object,
    default: null
  },
  /** Set de IDs de criaturas disponibles actualmente */
  availableIds: {
    type: Set,
    default: () => new Set()
  }
})

defineEmits(['select'])

/** ID único para asociar título con navegación */
const listId = computed(() => `critter-list-${Math.random().toString(36).slice(2, 9)}`)
</script>
