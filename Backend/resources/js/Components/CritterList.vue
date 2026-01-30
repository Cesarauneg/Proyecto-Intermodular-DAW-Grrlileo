<template>
  <div class="w-full lg:w-[30%] bg-white/80 backdrop-blur border-b-4 lg:border-b-0 lg:border-r-4 border-green-300 p-4 flex flex-col h-64 lg:h-full shadow-lg">
    <h2 class="text-xl lg:text-2xl font-bold mb-4 text-green-800 text-center pb-2 border-b-2 border-green-300">
      {{ icon }} {{ title }}
    </h2>

    <!-- Scroll solo en la lista -->
    <div class="flex-1 overflow-y-auto px-1">
      <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-3 gap-3">
        <CritterGridItem
          v-for="item in items"
          :key="item.id"
          :item="item"
          :is-selected="selectedItem && selectedItem.id === item.id"
          :is-available="availableIds.has(item.id)"
          @select="$emit('select', item)"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import CritterGridItem from './CritterGridItem.vue'

defineProps({
  title: {
    type: String,
    default: 'Todos los bichos'
  },
  icon: {
    type: String,
    default: '🦋'
  },
  items: {
    type: Array,
    default: () => []
  },
  selectedItem: {
    type: Object,
    default: null
  },
  availableIds: {
    type: Set,
    default: () => new Set()
  }
})

defineEmits(['select'])
</script>