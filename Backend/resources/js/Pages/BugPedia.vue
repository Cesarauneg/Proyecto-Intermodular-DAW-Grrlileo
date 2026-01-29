<template>
  <div class="flex h-screen">
    <!-- Lista de bichos -->
    <div class="w-1/4 bg-gray-100 border-r border-gray-300 p-4 h-full">
      <BichosList
        :bugs="bugs"
        :selectedBug="selectedBug"
        @select="selectedBug = $event"
      />
    </div>

    <!-- Detalles -->
    <BichosDetail :bug="selectedBug" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import BichosList from '@/Components/BugList.vue'
import BichosDetail from '@/Components/BugDetail.vue'

const bugs = ref([])
const selectedBug = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/bugs')
  bugs.value = res.data
})
</script>
