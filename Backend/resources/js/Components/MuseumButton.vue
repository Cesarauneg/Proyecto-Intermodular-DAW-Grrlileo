<template>
  <button
    @click="toggleMuseum"
    :class="[
      'relative z-50 w-14 h-14 sm:w-16 sm:h-16 rounded-full border-4 transition-all duration-300 transform hover:scale-110 active:scale-95 shadow-lg flex items-center justify-center',
      isInMuseum 
        ? 'bg-gradient-to-br from-amber-400 to-green-500 border-amber-300 hover:from-amber-500 hover:to-orange-600' 
        : 'bg-gray-200 border-gray-300 hover:bg-gray-300'
    ]"
    :title="isInMuseum ? 'Quitar del museo' : 'Agregar al museo'"
  >
    <img
      :src="museumIcon"
      alt="Museo"
      :class="[
        'w-8 h-8 sm:w-10 sm:h-10 pointer-events-none transition-all duration-300',
        isInMuseum ? 'opacity-100' : 'opacity-40'
      ]"
    />
  </button>
</template>

<script setup>
defineProps({
  isInMuseum: {
    type: Boolean,
    default: false
  },
  museumIcon: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['toggle'])

const toggleMuseum = () => {
  emit('toggle')
}
</script>