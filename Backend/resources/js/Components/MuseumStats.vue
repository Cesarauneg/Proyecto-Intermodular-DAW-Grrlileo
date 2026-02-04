<script setup>
import { computed } from 'vue';
import { Doughnut, Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  BarElement
} from 'chart.js';

ChartJS.register(
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  BarElement
);

const props = defineProps({
  stats: Object,
  maximos: Object
});

// Definimos los colores basados en el CSS para cada categoría
const categorias = [
  { key: 'peces', label: 'Peces', color: '#6bbde3' },      
  { key: 'bichos', label: 'Bichos', color: '#6dba6d' },    
  { key: 'arte', label: 'Arte', color: '#f2ba52' },       
  { key: 'fosiles', label: 'Fósiles', color: '#662028' },   
  { key: 'criaturas', label: 'Criaturas', color: '#3a8bbf' } 
];

const totalDonado = computed(() =>
  Object.values(props.stats).reduce((a, b) => a + b, 0)
);

const totalPosible = computed(() =>
  Object.values(props.maximos).reduce((a, b) => a + b, 0)
);

const porcentajeGlobal = computed(() =>
  ((totalDonado.value / totalPosible.value) * 100).toFixed(1)
);

const doughnutData = computed(() => ({
  labels: ['Donado', 'Restante'],
  datasets: [{
    data: [totalDonado.value, totalPosible.value - totalDonado.value],
    backgroundColor: ['#f2ba52', '#faf8ef'], 
    borderWidth: 3,
    borderColor: '#d9e4d0', 
    hoverOffset: 4
  }]
}));


const barData = computed(() => ({
  labels: categorias.map(c => c.label),
  datasets: [{
    label: 'Progreso',
    data: categorias.map(c => 
      ((props.stats[c.key] / props.maximos[c.key]) * 100).toFixed(1)
    ),
    // Asignamos el array de colores para que cada barra sea distinta
    backgroundColor: categorias.map(c => c.color),
    borderRadius: 20, 
    borderSkipped: false,
  }]
}));

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: '#3b3022',
      titleFont: { family: 'Nunito', size: 14 },
      bodyFont: { family: 'Nunito', size: 13 },
      callbacks: {
        label: (context) => ` Completado: ${context.parsed.y}%`
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      max: 100,
      ticks: {
        color: '#9b8e7f',
        font: { family: 'Nunito', weight: 'bold' },
        callback: v => `${v}%`
      },
      grid: { color: '#f5faf0' }
    },
    x: {
      ticks: { 
        color: '#3b3022',
        font: { family: 'Nunito', weight: 'bold', size: 12 } 
      },
      grid: { display: false }
    }
  }
};
</script>

<template>
  <div class="space-y-8">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
      <div
        v-for="cat in categorias"
        :key="cat.key"
        class="ac-card text-center py-4 border-b-4"
        :style="{ borderColor: cat.color }"
      >
        <p class="text-[10px] font-black uppercase tracking-widest text-[var(--ac-text-light)] mb-1">
          {{ cat.label }}
        </p>
        <p class="text-2xl font-black text-[var(--ac-text-primary)]">
          {{ stats[cat.key] }}
          <span class="text-xs opacity-40 font-bold">/{{ maximos[cat.key] }}</span>
        </p>
        <div class="w-full bg-gray-100 h-1.5 mt-2 rounded-full overflow-hidden">
            <div 
                class="h-full transition-all duration-500" 
                :style="{ width: (stats[cat.key]/maximos[cat.key]*100) + '%', backgroundColor: cat.color }"
            ></div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="ac-card relative overflow-hidden">
        <div class="absolute -top-4 -right-4 w-16 h-16 bg-[var(--ac-bg-secondary)] rounded-full opacity-50"></div>
        
        <h3 class="text-sm font-black uppercase tracking-tighter text-[var(--ac-text-secondary)] mb-6 flex items-center gap-2">
          <span class="w-2 h-4 bg-[var(--ac-gold)] rounded-full"></span>
          Progreso Total del Museo
        </h3>
        
        <div class="relative h-64">
          <Doughnut
            :data="doughnutData"
            :options="{ cutout: '75%', maintainAspectRatio: false, plugins: { legend: { display: false } } }"
          />
          <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-4xl font-black text-[var(--ac-text-primary)] leading-none">
              {{ porcentajeGlobal }}%
            </span>
            <span class="text-[10px] uppercase font-bold text-[var(--ac-text-light)] mt-1">Donado</span>
          </div>
        </div>
      </div>

      <div class="ac-card">
        <h3 class="text-sm font-black uppercase tracking-tighter text-[var(--ac-text-secondary)] mb-6 flex items-center gap-2">
          <span class="w-2 h-4 bg-[var(--ac-green)] rounded-full"></span>
          Detalle por Categoría
        </h3>
        <div class="h-64">
          <Bar :data="barData" :options="barOptions" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.ac-card {
    transition: transform 0.2s ease;
}
.ac-card:hover {
    transform: translateY(-3px);
}
</style>