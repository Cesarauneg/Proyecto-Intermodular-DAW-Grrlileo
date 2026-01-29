<template>
  <div class="flex-1 flex items-center justify-center p-6 h-full">
    <div v-if="bug" class="max-w-3xl w-full flex flex-col items-center gap-6">
      <!-- Imagen grande del bicho -->
      <img
        :src="bug.image"
        :alt="bug.name_es"
        class="w-full max-w-md h-96 object-contain rounded-lg shadow-2xl"
      />

      <!-- Tarjeta de información -->
      <div class="bg-green-100 shadow-xl rounded-2xl p-6 w-full max-w-md border border-green-300">
        <h1 class="text-3xl font-bold mb-4 text-center">{{ bug.name_es }}</h1>
        <ul class="space-y-2 text-gray-800">
          <li><strong>Tipo:</strong> {{ bug.type }}</li>
          <li><strong>Precio:</strong> {{ bug.price }} monedas</li>
          <li><strong>Rareza:</strong> {{ bug.rarity }}</li>
          <li><strong>Estación:</strong> {{ bug.season }}</li>
          <li><strong>Hora:</strong> {{ bug.time }}</li>
        </ul>
      </div>
    </div>

    <div v-else class="text-gray-400 text-lg text-center">
      Selecciona un bicho para ver su información
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  bug: Object
})
</script>
