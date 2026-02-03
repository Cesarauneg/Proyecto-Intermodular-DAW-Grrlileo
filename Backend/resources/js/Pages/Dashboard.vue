<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MuseumStats from '@/Components/MuseumStats.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    maximos: Object
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold leading-tight text-green-800">
                🏛️ Registro del Museo
            </h2>
        </template>

        <div class="py-12 bg-[#fdf6e3] min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="mb-8 overflow-hidden bg-[#789d4a] shadow-sm sm:rounded-2xl border-b-4 border-[#5d7a39]">
                    <div class="p-8 text-white">
                        <h3 class="text-2xl font-bold italic text-[#fefae0]">¡Hola, {{ $page.props.auth.user.name }}!</h3>
                        <p class="mt-2 text-[#fefae0] opacity-90">Sigue así, Sócrates está muy orgulloso de tus hallazgos.</p>
                    </div>
                </div>

                <div v-if="props.stats && props.maximos">
                    <MuseumStats :stats="props.stats" :maximos="props.maximos" />
                </div>

                <div v-if="props.stats" class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-8">
                    <div v-for="(val, key) in props.stats" :key="key" 
                         class="bg-[#efe7d1] p-4 rounded-xl border-2 border-[#d6ccad] text-center shadow-sm">
                        <p class="text-[#8c7851] text-xs uppercase font-bold">{{ key }}</p>
                        <p class="text-2xl font-black text-[#5d4037]">{{ val }} <span class="text-sm opacity-50">/ {{ props.maximos[key] }}</span></p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>