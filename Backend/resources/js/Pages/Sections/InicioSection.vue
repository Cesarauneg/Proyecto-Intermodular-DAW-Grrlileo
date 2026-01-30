<script setup>
import { ref, watch, onMounted } from 'vue';
import confetti from 'canvas-confetti';
import VillagerMarquee from '@/Components/VillagerMarquee.vue';

defineProps({
    villagers: {
        type: Array,
        default: () => []
    }
});

const isVisible = ref(false);
const isGiftOpen = ref(false);

// Typewriter effect
const fullMessage = "Hoy es el cumpleaños de Bongo! No olvides felicitarlo y darle muchos mimos.";
const displayedText = ref('');
const isTyping = ref(false);

watch(isGiftOpen, (open) => {
    if (open) {
        // Iniciar typewriter
        displayedText.value = '';
        isTyping.value = true;

        setTimeout(() => {
            let i = 0;
            const tick = () => {
                if (i < fullMessage.length && isGiftOpen.value) {
                    i++;
                    displayedText.value = fullMessage.slice(0, i);
                    setTimeout(tick, 35);
                } else {
                    isTyping.value = false;
                }
            };
            tick();
        }, 500);
    } else {
        // Detener
        displayedText.value = '';
        isTyping.value = false;
    }
});

const toggleGift = () => {
    isGiftOpen.value = !isGiftOpen.value;

    if (isGiftOpen.value) {
        confetti({
            particleCount: 150,
            spread: 70,
            origin: { y: 0.6 },
            colors: ['#78c850', '#f8d030', '#6890f0', '#f85888', '#a8b820', '#98d8d8']
        });
    }
};

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 300);
});
</script>

<template>
    <section class="inicio-section" aria-labelledby="inicio-title">
        <!-- Contenedor principal (Canela + Regalo) -->
        <div class="inicio-main">
            <div class="inicio-content">
                <!-- Contenedor de Canela y Diálogo -->
                <div class="welcome-container" :class="{ visible: isVisible }">
                    <!-- Diálogo de bienvenida -->
                    <div class="dialog-box">
                        <h1 id="inicio-title">Bienvenido a Canela's Desk</h1>
                        <p>Aqui puedes crear tus propios museos de:</p>
                        <img
                            src="/images/Inicio/AClogo.png"
                            alt="Animal Crossing Logo"
                            class="ac-logo"
                            width="200"
                        />
                    </div>

                    <!-- Canela -->
                    <div class="canela-container">
                        <img
                            src="/images/Inicio/canela.png"
                            alt="Canela dándote la bienvenida"
                            class="canela-img"
                        />
                    </div>
                </div>

                <!-- Regalo Sorpresa -->
                <div
                    class="gift-container relative w-32 h-40 cursor-pointer mt-8 select-none"
                    :class="{ visible: isVisible }"
                    @click="toggleGift"
                >
                    <!-- Contenido Sorpresa (Fondo) -->
                    <div
                        class="absolute inset-x-0 bottom-0 flex justify-center transition-all duration-500 ease-out"
                        :class="isGiftOpen ? '-translate-y-24 opacity-100' : 'translate-y-0 opacity-0'"
                    >
                        <img
                            src="/images/perro-guitarra.png"
                            alt="Sorpresa"
                            class="w-24 h-24 object-contain drop-shadow-lg"
                        />
                    </div>

                    <!-- Base de la Caja (Medio) -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-24 h-16 bg-red-500 rounded-sm shadow-lg">
                        <!-- Cinta vertical -->
                        <div class="absolute left-1/2 -translate-x-1/2 w-4 h-full bg-yellow-400"></div>
                    </div>

                    <!-- Tapa de la Caja (Frente) -->
                    <div
                        class="absolute bottom-14 left-1/2 -translate-x-1/2 transition-all duration-500 ease-out origin-bottom"
                        :class="isGiftOpen ? '-translate-y-16 -rotate-45' : 'translate-y-0 rotate-0'"
                    >
                        <!-- Tapa -->
                        <div class="relative w-28 h-6 bg-red-600 rounded-sm shadow-md">
                            <!-- Cinta horizontal -->
                            <div class="absolute top-1/2 -translate-y-1/2 w-full h-4 bg-yellow-400"></div>
                        </div>

                        <!-- Lazo -->
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 flex items-end justify-center">
                            <!-- Lazo izquierdo -->
                            <div class="w-4 h-5 bg-yellow-400 rounded-full -rotate-[30deg] -mr-1 origin-bottom"></div>
                            <!-- Centro del lazo -->
                            <div class="w-3 h-3 bg-yellow-500 rounded-full z-10"></div>
                            <!-- Lazo derecho -->
                            <div class="w-4 h-5 bg-yellow-400 rounded-full rotate-[30deg] -ml-1 origin-bottom"></div>
                        </div>
                    </div>

                    <!-- Texto indicativo -->
                    <p
                        class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs text-amber-700 font-medium whitespace-nowrap transition-opacity duration-300"
                        :class="isGiftOpen ? 'opacity-0' : 'opacity-100'"
                    >
                        Haz clic para abrir
                    </p>
                </div>

                <!-- Mensaje de Felicitación (Typewriter) -->
                <transition
                    enter-active-class="transition-all duration-500 ease-out"
                    enter-from-class="opacity-0 translate-y-4 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition-all duration-300 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-4 scale-95"
                >
                    <div
                        v-if="isGiftOpen"
                        class="mt-10 px-6 py-4 bg-[#FFF8DC] border-4 border-amber-400 rounded-3xl shadow-lg max-w-sm"
                    >
                        <p class="text-amber-900 text-base leading-relaxed font-medium">
                            {{ displayedText }}<span
                                class="inline-block w-2 h-5 ml-1 bg-amber-600 align-middle"
                                :class="isTyping ? 'animate-pulse' : 'animate-blink'"
                            ></span>
                        </p>
                    </div>
                </transition>
            </div>
        </div>

        <!-- Carrusel de Aldeanos (siempre abajo) -->
        <div v-if="villagers && villagers.length > 0" class="villager-marquee-section">
            <h2 class="marquee-title">Conoce a nuestros vecinos</h2>
            <VillagerMarquee :villagers="villagers" />
        </div>
    </section>
</template>

<style scoped src="@/../css/pages/sections/inicio.css"></style>
