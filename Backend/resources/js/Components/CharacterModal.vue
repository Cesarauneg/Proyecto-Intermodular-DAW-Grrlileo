<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl max-w-md w-full p-6 relative">

      <!-- Botón cerrar -->
      <button
        class="absolute top-4 right-4 text-gray-500 hover:text-black text-xl"
        @click="$emit('close')"
      >
        ✕
      </button>

      <!-- Imagen -->
      <div class="flex justify-center mt-6 mb-4">
        <img
          :src="character.image"
          alt="Imagen del personaje"
          class="max-h-56 object-contain"
        />
      </div>

      <!-- Info -->
      <h2 class="text-2xl font-bold text-center">
        {{ character.name_es }}
      </h2>

      <p class="text-center text-gray-500 italic mt-1">
        "{{ character.catch_es }}"
      </p>

      <ul class="mt-4 space-y-2 text-sm">
        <li><strong>Personalidad:</strong> {{ character.personality }}</li>
        <li><strong>Especie:</strong> {{ character.species }}</li>
        <li><strong>Género:</strong> {{ character.gender }}</li>
        <li><strong>Cumpleaños:</strong> {{ character.birthday_string }}</li>
        <li><strong>Hobby:</strong> {{ character.hobby }}</li>
        <li><strong>Frase:</strong> {{ character.saying }}</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
defineProps({
  character: Object
})
</script>
