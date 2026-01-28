<template>
  <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:scale-105 transition-transform duration-200 cursor-pointer">
    <img :src="imagePath" class="w-full h-48 object-cover" />
    <div class="p-4">
      <h3 class="text-lg font-semibold text-gray-800">{{ character.name_es }}</h3>
      <p class="text-gray-500">{{ character.species }}</p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  character: Object
})

const imagePath = `${props.character.image}`
</script>
