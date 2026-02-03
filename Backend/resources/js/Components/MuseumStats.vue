<script setup>
import { computed } from 'vue';
import { Doughnut, Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale, BarElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale, BarElement);

const props = defineProps({
    stats: { type: Object, required: true },
    maximos: { type: Object, required: true }
});

const totalDonado = computed(() => Object.values(props.stats).reduce((a, b) => a + b, 0));
const totalPosible = computed(() => Object.values(props.maximos).reduce((a, b) => a + b, 0));
const porcentajeGlobal = computed(() => ((totalDonado.value / totalPosible.value) * 100).toFixed(1));

const doughnutData = computed(() => ({
    labels: ['Donado', 'Restante'],
    datasets: [{
        data: [totalDonado.value, totalPosible.value - totalDonado.value],
        backgroundColor: ['#a3c585', '#e9e4d0'],
        borderWidth: 2,
        borderColor: '#fdf6e3'
    }]
}));

const barData = computed(() => {
    const categorias = ['peces', 'bichos', 'arte', 'fosiles', 'criaturas'];
    const nombresLabels = ['Peces', 'Bichos', 'Arte', 'Fósiles', 'Criaturas'];
    
    const porcentajes = categorias.map(cat => {
        return ((props.stats[cat] / props.maximos[cat]) * 100).toFixed(1);
    });

    return {
        labels: nombresLabels,
        datasets: [{
            label: 'Porcentaje Completado (%)',
            data: porcentajes,
            backgroundColor: '#89b4fa',
            borderRadius: 8,
        }]
    };
});

const barOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: { 
            beginAtZero: true, 
            max: 100,
            ticks: { 
                color: '#8c7851',
                callback: (value) => value + '%' 
            } 
        },
        x: { ticks: { color: '#8c7851' } }
    },
    plugins: { 
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: function(context) {
                    const catKey = ['peces', 'bichos', 'arte', 'fosiles', 'criaturas'][context.dataIndex];
                    const realValue = props.stats[catKey];
                    const maxValue = props.maximos[catKey];
                    return `${context.parsed.y}% (${realValue} de ${maxValue})`;
                }
            }
        }
    }
};
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-[#efe7d1] p-6 rounded-xl border-2 border-[#d6ccad] shadow-sm">
            <h3 class="text-[#8c7851] text-sm font-bold mb-4 uppercase tracking-widest">Progreso del Museo</h3>
            <div class="relative h-64">
                <Doughnut :data="doughnutData" :options="{ cutout: '70%', maintainAspectRatio: false }" />
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-3xl font-bold text-[#5d4037]">{{ porcentajeGlobal }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-[#efe7d1] p-6 rounded-xl border-2 border-[#d6ccad] shadow-sm">
            <h3 class="text-[#8c7851] text-sm font-bold mb-4 uppercase tracking-widest">Completado por Categoría</h3>
            <div class="h-64">
                <Bar :data="barData" :options="barOptions" />
            </div>
        </div>
    </div>
</template>