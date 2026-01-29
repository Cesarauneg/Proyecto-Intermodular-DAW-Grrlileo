<template>
  <div class="flex flex-col h-full">
    <h2 class="text-xl font-bold mb-4">Todos los bichos</h2>

    <div class="flex-1 overflow-y-auto">
      <div class="grid grid-cols-3 gap-2">
        <div
          v-for="bug in bugs"
          :key="bug.id"
          @click="$emit('select', bug)"
          :class="[
            'cursor-pointer p-1 rounded border hover:border-blue-400 transition',
            selectedBug && selectedBug.id === bug.id ? 'border-blue-500' : 'border-transparent'
          ]"
        >
          <img
            :src="bug.icon" <!-- icon para la miniatura -->
            alt="miniatura"
            class="w-full h-16 object-contain mb-1"
          />
          <p class="text-xs text-center truncate">{{ bug.name }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  bugs: Array,
  selectedBug: Object
})
</script>
