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
    borderRadius: 0,
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
  <div class="stats-container">
    <!-- Tarjetas superiores con flexbox para evitar solapamiento -->
    <div class="cards-grid">
      <div
        v-for="cat in categorias"
        :key="cat.key"
        class="stat-card"
        :style="{ borderBottomColor: cat.color }"
      >
        <p class="card-label">{{ cat.label }}</p>
        <p class="card-value">
          {{ stats[cat.key] }}
          <span class="card-max">/{{ maximos[cat.key] }}</span>
        </p>
        <div class="progress-bar">
            <div
                class="progress-fill"
                :style="{ width: (stats[cat.key]/maximos[cat.key]*100) + '%', backgroundColor: cat.color }"
            ></div>
        </div>
      </div>
    </div>

    <!-- Gráfico de barras - Rompe los márgenes para usar TODO el ancho -->
    <div class="chart-card bar-chart-card">
      <h3 class="chart-title">
        <span class="title-dot" style="background: var(--ac-green);"></span>
        Detalle por Categoría
      </h3>
      <div class="bar-chart-container">
        <Bar :data="barData" :options="barOptions" />
      </div>
    </div>

    <!-- Gráfico de dona - Más pequeño y centrado -->
    <div class="chart-card doughnut-chart-card">
      <h3 class="chart-title">
        <span class="title-dot" style="background: var(--ac-gold);"></span>
        Progreso Total del Museo
      </h3>

      <div class="doughnut-chart-container">
        <Doughnut
          :data="doughnutData"
          :options="{ cutout: '75%', maintainAspectRatio: false, plugins: { legend: { display: false } } }"
        />
        <div class="doughnut-center">
          <span class="doughnut-percentage">{{ porcentajeGlobal }}%</span>
          <span class="doughnut-label">Donado</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.stats-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Tarjetas superiores */
.cards-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.75rem;
  padding: 0 0.5rem;
}

.stat-card {
  background: #faf8ef;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  padding: 0.75rem 1rem;
  text-align: center;
  border-bottom: 4px solid;
  flex: 1 1 auto;
  min-width: 120px;
  max-width: 160px;
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.card-label {
  font-size: 0.625rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--ac-text-light);
  margin-bottom: 0.25rem;
}

.card-value {
  font-size: 1.5rem;
  font-weight: 900;
  color: var(--ac-text-primary);
}

.card-max {
  font-size: 0.75rem;
  opacity: 0.4;
  font-weight: bold;
}

.progress-bar {
  width: 100%;
  background: #e5e7eb;
  height: 0.375rem;
  margin-top: 0.5rem;
  border-radius: 9999px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  transition: all 0.5s ease;
}

/* Tarjetas de gráficos */
.chart-card {
  background: #faf8ef;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  padding: 1.5rem;
  margin: 0 -1rem;
}

.chart-title {
  font-size: 0.875rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: -0.025em;
  color: var(--ac-text-secondary);
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.title-dot {
  width: 8px;
  height: 16px;
  border-radius: 9999px;
}

/* Gráfico de barras */
.bar-chart-container {
  height: 450px;
  width: 100%;
}

/* Gráfico de dona */
.doughnut-chart-card {
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.doughnut-chart-container {
  position: relative;
  height: 280px;
  width: 100%;
}

.doughnut-center {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.doughnut-percentage {
  font-size: 2.5rem;
  font-weight: 900;
  color: var(--ac-text-primary);
  line-height: 1;
}

.doughnut-label {
  font-size: 0.625rem;
  text-transform: uppercase;
  font-weight: bold;
  color: var(--ac-text-light);
  margin-top: 0.25rem;
}

/* Media queries para tablets */
@media (max-width: 1024px) {
  .bar-chart-container {
    height: 350px;
  }

  .doughnut-chart-container {
    height: 240px;
  }

  .doughnut-percentage {
    font-size: 2rem;
  }
}

/* Media queries para móviles */
@media (max-width: 768px) {
  .stats-container {
    gap: 1rem;
  }

  .cards-grid {
    gap: 0.5rem;
  }

  .stat-card {
    min-width: 100px;
    max-width: 140px;
    padding: 0.5rem 0.75rem;
  }

  .card-label {
    font-size: 0.5rem;
  }

  .card-value {
    font-size: 1.25rem;
  }

  .card-max {
    font-size: 0.625rem;
  }

  .chart-card {
    padding: 1rem;
    margin: 0 -0.5rem;
    border-radius: 12px;
  }

  .chart-title {
    font-size: 0.75rem;
    margin-bottom: 1rem;
  }

  .bar-chart-container {
    height: 280px;
  }

  .doughnut-chart-container {
    height: 200px;
  }

  .doughnut-percentage {
    font-size: 1.75rem;
  }
}

/* Media queries para móviles pequeños */
@media (max-width: 480px) {
  .stat-card {
    min-width: 85px;
    max-width: 120px;
  }

  .bar-chart-container {
    height: 240px;
  }

  .doughnut-chart-container {
    height: 180px;
  }

  .doughnut-percentage {
    font-size: 1.5rem;
  }

  .chart-card {
    padding: 0.75rem;
  }
}
</style>
