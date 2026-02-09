<!--
  @fileoverview Drawer lateral reutilizable para móvil.
  Se desliza desde la izquierda con animación y backdrop.
-->
<template>
  <Transition name="drawer">
    <div
      v-if="open"
      class="lg:hidden absolute inset-0 z-20 flex"
      role="dialog"
      aria-modal="true"
      :aria-label="ariaLabel"
    >
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-black/50"
        aria-hidden="true"
        @click="$emit('close')"
      />

      <!-- Panel del drawer -->
      <aside
        class="relative w-4/5 max-w-xs h-full bg-white shadow-2xl overflow-hidden"
        role="navigation"
      >
        <slot />
      </aside>
    </div>
  </Transition>
</template>

<script setup>
defineProps({
  /** Controla si el drawer está abierto */
  open: {
    type: Boolean,
    default: false
  },
  /** Etiqueta ARIA para accesibilidad */
  ariaLabel: {
    type: String,
    default: 'Menú de navegación'
  }
})

defineEmits(['close'])
</script>

<style scoped>
/* Transiciones del drawer */
.drawer-enter-active,
.drawer-leave-active {
  transition: opacity 0.3s ease;
}

.drawer-enter-active > aside,
.drawer-leave-active > aside {
  transition: transform 0.3s ease;
}

.drawer-enter-from,
.drawer-leave-to {
  opacity: 0;
}

.drawer-enter-from > aside,
.drawer-leave-to > aside {
  transform: translateX(-100%);
}
</style>
