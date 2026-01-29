<template>
  <div class="w-full bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="flex flex-col md:flex-row gap-4 items-center">
      <div class="relative flex-grow w-full">
        <input 
          :value="modelValue"
          @input="$emit('update:modelValue', $event.target.value)"
          type="text" 
          :placeholder="placeholder" 
          class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-400 outline-none transition-all"
        />
      </div>

      <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: String,
  placeholder: { type: String, default: 'Buscar...' }
})
defineEmits(['update:modelValue'])
</script>