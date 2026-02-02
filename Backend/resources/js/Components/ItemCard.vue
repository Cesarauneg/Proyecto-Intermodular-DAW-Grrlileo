<template>
  <article class="flex flex-col h-full bg-white shadow-sm rounded-lg overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer border border-gray-100">
    
    <div class="w-full h-24 sm:h-32 bg-gray-50 flex items-center justify-center p-2">
      <img 
        :src="computedImagePath" 
        :alt="name_es" 
        @error="handleError" 
        class="max-w-full max-h-full object-contain" 
      />
    </div>

    <div class="p-2 flex flex-col flex-grow">
<h2 class="text-sm sm:text-base font-bold text-gray-800 line-clamp-2 leading-tight min-h-[2.5rem]">
  {{ capitalize(name_es) }}
</h2>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { capitalize } from '@/Utils/formatters.js'

const props = defineProps({
  image: String,
  name_es: String,
  price: [Number, String]
})

const computedImagePath = computed(() => {
  if (!props.image) return '/images/placeholder.jpg'
  return props.image.startsWith('/') ? props.image : `/${props.image}`
})

const handleError = (e) => {
  e.target.src = '/images/placeholder.jpg'
}
</script>