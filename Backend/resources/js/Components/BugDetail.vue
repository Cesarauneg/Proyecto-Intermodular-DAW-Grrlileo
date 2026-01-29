<template>
  <div class="flex-1 p-6 flex flex-col items-center justify-start overflow-y-auto h-full">
    <div v-if="bug" class="max-w-md w-full">
      <!-- Imagen grande -->
      <img
        :src="bug.image" <!-- imagen PNG grande -->
        alt="imagen del bicho"
        class="w-full h-64 object-contain mb-4"
      />

      <!-- Nombre -->
      <h1 class="text-3xl font-bold mb-2">{{ bug.name }}</h1>

      <!-- Card con información -->
      <div class="bg-white shadow rounded p-4 w-full">
        <ul class="space-y-1 text-gray-700">
          <li><strong>Tipo:</strong> {{ bug.type }}</li>
          <li><strong>Precio:</strong> {{ bug.price }} monedas</li>
          <li><strong>Rareza:</strong> {{ bug.rarity }}</li>
          <li><strong>Estación:</strong> {{ bug.season }}</li>
          <li><strong>Hora:</strong> {{ bug.time }}</li>
        </ul>
      </div>
    </div>

    <div v-else class="text-gray-400">Selecciona un bicho para ver su información</div>
  </div>
</template>

<script setup>
const props = defineProps({
  bug: Object
})
</script>
