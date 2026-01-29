<template>
  <ItemCard 
    :image="fish.image" 
    :name_es="fish.name_es" 
    :price="fish.price"
  >
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