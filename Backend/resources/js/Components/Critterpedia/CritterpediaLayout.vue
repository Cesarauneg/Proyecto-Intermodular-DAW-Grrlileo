<!--
  @fileoverview Layout compartido para todas las páginas *Pedia.
  Maneja la estructura común: lista, drawer móvil y área de detalle.
-->
<template>
  <article
    :class="[
      'relative flex flex-col lg:flex-row h-full rounded-xl overflow-hidden',
      'bg-gradient-to-br',
      backgroundClass
    ]"
    :aria-label="ariaLabel"
  >
    <!-- Lista principal: visible en desktop siempre, en móvil solo sin selección -->
    <CritterList
      :class="selectedItem ? 'hidden lg:flex' : 'flex'"
      :title="listTitle"
      :items="filteredItems"
      :selected-item="selectedItem"
      :available-ids="availableIds"
      @select="$emit('select', $event)"
    />

    <!-- Drawer móvil -->
    <MobileDrawer
      :open="drawerOpen"
      :aria-label="`Menú de ${listTitle}`"
      @close="$emit('close-drawer')"
    >
      <CritterList
        :title="listTitle"
        :items="filteredItems"
        :selected-item="selectedItem"
        :available-ids="availableIds"
        class="h-full"
        @select="$emit('select', $event)"
      />
    </MobileDrawer>

    <!-- Área de detalle -->
    <main
      :class="[
        'flex-1 flex items-center justify-center p-4',
        selectedItem ? 'flex' : 'hidden lg:flex'
      ]"
      :aria-live="selectedItem ? 'polite' : 'off'"
    >
      <slot v-if="selectedItem" name="detail" />
      <EmptyState
        v-else
        :icon="emptyIcon"
        :title="emptyTitle"
      />
    </main>
  </article>
</template>

<script setup>
import CritterList from '@/Components/CritterList.vue'
import MobileDrawer from '@/Components/Base/MobileDrawer.vue'
import EmptyState from '@/Components/EmptyState.vue'

defineProps({
  /** Título de la lista */
  listTitle: {
    type: String,
    required: true
  },
  /** Items filtrados a mostrar */
  filteredItems: {
    type: Array,
    required: true
  },
  /** Item actualmente seleccionado */
  selectedItem: {
    type: Object,
    default: null
  },
  /** Set de IDs de items disponibles */
  availableIds: {
    type: Set,
    default: () => new Set()
  },
  /** Estado del drawer móvil */
  drawerOpen: {
    type: Boolean,
    default: false
  },
  /** Clases de fondo (gradiente) */
  backgroundClass: {
    type: String,
    default: 'from-green-50 to-blue-50'
  },
  /** Etiqueta ARIA del artículo */
  ariaLabel: {
    type: String,
    required: true
  },
  /** Icono para estado vacío */
  emptyIcon: {
    type: String,
    default: '🔍'
  },
  /** Título para estado vacío */
  emptyTitle: {
    type: String,
    default: 'Selecciona un elemento'
  }
})

defineEmits(['select', 'close-drawer'])
</script>
