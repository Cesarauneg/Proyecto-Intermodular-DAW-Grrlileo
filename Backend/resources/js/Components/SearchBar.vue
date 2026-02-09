<template>
  <section class="w-full bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6" role="search">
    <form class="flex flex-col gap-4" @submit.prevent role="search">
      <!-- Campo de búsqueda -->
      <div class="w-full">
        <label :for="inputId" class="sr-only">{{ placeholder }}</label>
        <input
          :id="inputId"
          :value="modelValue"
          @input="$emit('update:modelValue', $event.target.value)"
          type="search"
          :placeholder="placeholder"
          class="w-full px-4 py-2 bg-gray-50 border-none rounded-lg text-sm
                 focus:ring-2 focus:ring-green-400 focus:bg-white
                 transition-all outline-none"
          autocomplete="off"
        />
      </div>

      <!-- Filtros (slot) -->
      <div class="flex flex-wrap gap-2" role="group" aria-label="Filtros de búsqueda">
        <slot />
      </div>
    </form>
  </section>
</template>

<script setup>
/**
 * @fileoverview Componente de barra de búsqueda reutilizable.
 * Incluye un input de búsqueda y un slot para filtros adicionales.
 */

import { computed } from 'vue'

const props = defineProps({
  /** Valor del campo de búsqueda (v-model) */
  modelValue: {
    type: String,
    default: ''
  },
  /** Texto placeholder del input */
  placeholder: {
    type: String,
    default: 'Buscar...'
  }
})

defineEmits(['update:modelValue'])

/**
 * ID único para asociar label con input.
 * Generado una vez por instancia del componente.
 */
const inputId = computed(() => `search-${Math.random().toString(36).slice(2, 9)}`)
</script>
