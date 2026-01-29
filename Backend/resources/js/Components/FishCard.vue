<template>
  <ItemCard 
    :image="fish.image" 
    :name_es="fish.name_es" 
    :price="fish.price"
  >
    <div class="flex flex-col gap-1">
      <span class="flex items-center gap-1">
        📍 {{ fish.location || 'Desconocida' }}
      </span>
      <span v-if="fish.rarity" class="italic text-blue-500">
        ✨ {{ fish.rarity }}
      </span>
    </div>
  </ItemCard>
</template>

<script setup>
import ItemCard from './ItemCard.vue'

defineProps({
  fish: {
    type: Object,
    required: true
  }
})
</script>