<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    embedded: { type: Boolean, default: false },
    level: { type: Number, required: true },
    pairs: { type: Number, required: true },
    cards: { type: Array, required: true },
    bestTime: { type: Number, default: null },
    bestMoves: { type: Number, default: null },
    memorizeSeconds: { type: Number, default: 3 },
});

const cards = ref([]);
const flippedCards = ref([]);
const isMemorizing = ref(true);
const isLocked = ref(true);
const startTime = ref(null);
const elapsedMs = ref(0);
const timerId = ref(null);
const resultOpen = ref(false);
const currentTimeMs = ref(null);
const bestTime = ref(props.bestTime);
const moves = ref(0);
const bestMoves = ref(props.bestMoves);

const totalPairs = computed(() => props.pairs);
const matchedPairs = computed(() => {
    return Math.floor(cards.value.filter((card) => card.isMatched).length / 2);
});

const gridMap = {
    1: { cols: 6, rows: 3 },  // 18
    2: { cols: 6, rows: 4 },  // 24
    3: { cols: 8, rows: 4 },  // 32
    4: { cols: 8, rows: 5 },  // 40
    5: { cols: 10, rows: 5 }, // 50
};

const gridConfig = computed(() => gridMap[props.level] || { cols: 6, rows: 3 });

const gridStyle = computed(() => ({
    gridTemplateColumns: `repeat(${gridConfig.value.cols}, minmax(0, 1fr))`,
    gridAutoRows: '1fr',
}));

const formatTimeSeconds = (seconds) => {
    if (seconds === null || seconds === undefined) return '--';
    const total = Math.max(0, seconds);
    const minutes = Math.floor(total / 60);
    const secs = (total % 60).toFixed(2).padStart(5, '0');
    return `${minutes}:${secs}`;
};

const formatTimeMs = (ms) => {
    if (!ms && ms !== 0) return '--';
    return formatTimeSeconds(ms / 1000);
};

const stopTimer = () => {
    if (timerId.value) {
        clearInterval(timerId.value);
        timerId.value = null;
    }
};

const startTimer = () => {
    startTime.value = performance.now();
    stopTimer();
    timerId.value = setInterval(() => {
        elapsedMs.value = performance.now() - startTime.value;
    }, 100);
};

const resetState = () => {
    cards.value = props.cards.map((card) => ({
        ...card,
        isFlipped: true,
        isMatched: false,
    }));
    flippedCards.value = [];
    isMemorizing.value = true;
    isLocked.value = true;
    resultOpen.value = false;
    currentTimeMs.value = null;
    elapsedMs.value = 0;
    moves.value = 0;
    stopTimer();
};

const beginRound = () => {
    if (!props.cards || props.cards.length === 0) return;
    resetState();

    setTimeout(() => {
        cards.value = cards.value.map((card) => ({
            ...card,
            isFlipped: false,
        }));
        isMemorizing.value = false;
        isLocked.value = false;
        startTimer();
    }, props.memorizeSeconds * 1000);
};

const finishGame = async () => {
    stopTimer();
    currentTimeMs.value = elapsedMs.value;
    resultOpen.value = true;

    const currentSeconds = Number((currentTimeMs.value / 1000).toFixed(3));
    if (bestTime.value === null || currentSeconds < bestTime.value) {
        bestTime.value = currentSeconds;
    }
    if (bestMoves.value === null || moves.value < bestMoves.value) {
        bestMoves.value = moves.value;
    }

    try {
        const response = await axios.post(route('memory-game.store'), {
            level: props.level,
            time_seconds: currentSeconds,
            moves: moves.value,
        });
        if (response?.data?.best_time !== undefined && response?.data?.best_time !== null) {
            bestTime.value = response.data.best_time;
        }
        if (response?.data?.best_moves !== undefined && response?.data?.best_moves !== null) {
            bestMoves.value = response.data.best_moves;
        }
    } catch (error) {
        console.error('Error guardando el puntaje', error);
    }
};

const handleFlip = (card) => {
    if (isMemorizing.value || isLocked.value || card.isMatched || card.isFlipped) return;

    card.isFlipped = true;
    flippedCards.value.push(card);

    if (flippedCards.value.length < 2) return;

    isLocked.value = true;
    moves.value += 1;
    const [first, second] = flippedCards.value;

    if (first.pair_id === second.pair_id) {
        first.isMatched = true;
        second.isMatched = true;
        flippedCards.value = [];
        isLocked.value = false;

        if (matchedPairs.value === totalPairs.value) {
            finishGame();
        }
        return;
    }

    setTimeout(() => {
        first.isFlipped = false;
        second.isFlipped = false;
        flippedCards.value = [];
        isLocked.value = false;
    }, 700);
};

const restart = () => {
    beginRound();
};

watch(
    () => [props.level, props.cards],
    () => {
        beginRound();
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    stopTimer();
});
</script>

<template>
    <Head v-if="!embedded" title="Memory" />
    <component :is="embedded ? 'div' : AuthenticatedLayout">
        <template v-if="!embedded" #header>
            <h1 class="text-xl font-bold text-[color:var(--ac-text-primary)]">Memory de Vecinos</h1>
        </template>

        <div v-if="embedded" class="flex items-center gap-3">
            <h2 class="text-lg font-bold text-[color:var(--ac-text-primary)]">Memory de Vecinos</h2>
            <span class="text-xs text-[color:var(--ac-text-light)]">Nivel {{ level }}</span>
        </div>

        <section class="space-y-6">
            <div class="flex flex-wrap items-center gap-4 py-3">
                <div class="rounded-full border border-[color:var(--ac-border)] bg-white/80 px-4 py-2 text-sm font-semibold">
                    Nivel {{ level }} · {{ pairs }} parejas
                </div>
            <div class="rounded-full border border-[color:var(--ac-border)] bg-white/80 px-3 py-2 text-sm flex items-center gap-2">
                <span>Tiempo:</span>
                <span class="font-semibold tabular-nums">{{ formatTimeMs(elapsedMs) }}</span>
            </div>
            <div class="rounded-full border border-[color:var(--ac-border)] bg-white/80 px-3 py-2 text-sm flex items-center gap-2">
                <span>Movimientos:</span>
                <span class="font-semibold tabular-nums">{{ moves }}</span>
            </div>
            <div class="rounded-full border border-[color:var(--ac-border)] bg-white/80 px-3 py-2 text-sm flex items-center gap-2">
                <span>Mejor:</span>
                <span class="font-semibold tabular-nums">{{ formatTimeSeconds(bestTime) }}</span>
            </div>
            <div class="rounded-full border border-[color:var(--ac-border)] bg-white/80 px-3 py-2 text-sm flex items-center gap-2">
                <span>Mejor movs:</span>
                <span class="font-semibold tabular-nums">{{ bestMoves ?? '--' }}</span>
            </div>
                <div v-if="isMemorizing" class="rounded-full bg-[color:var(--ac-green-light)] px-4 py-2 text-sm font-semibold text-[color:var(--ac-green-dark)]">
                    Memoriza {{ memorizeSeconds }}s
                </div>
                <button
                    type="button"
                    class="ml-auto rounded-full border border-[color:var(--ac-green-dark)] px-4 py-2 text-sm font-semibold text-[color:var(--ac-green-dark)] transition hover:bg-[color:var(--ac-green-dark)] hover:text-white"
                    @click="restart"
                >
                    Reiniciar
                </button>
            </div>

            <div class="grid gap-2 sm:gap-3 mx-auto w-full max-w-full place-content-center" :style="gridStyle">
                <button
                    v-for="card in cards"
                    :key="card.id"
                    type="button"
                    class="relative aspect-square w-full [perspective:1000px]"
                    @click="handleFlip(card)"
                    :disabled="isMemorizing || isLocked || card.isMatched"
                >
                    <span class="sr-only">{{ card.name }}</span>
                    <div
                        class="relative h-full w-full transition-transform duration-500 [transform-style:preserve-3d]"
                        :class="card.isFlipped || card.isMatched ? '[transform:rotateY(180deg)]' : '[transform:rotateY(0deg)]'"
                    >
                        <div
                            class="absolute inset-0 flex items-center justify-center rounded-2xl border border-[color:var(--ac-border)] bg-white shadow-md [backface-visibility:hidden]"
                        >
                            <img
                                src="/images/logos/favicon.png"
                                alt="Reverso de la carta"
                                class="h-1/2 w-1/2 max-h-full max-w-full opacity-70 object-contain"
                            />
                        </div>
                        <div
                            class="absolute inset-0 flex items-center justify-center rounded-2xl border border-[color:var(--ac-border)] bg-white shadow-md [backface-visibility:hidden] [transform:rotateY(180deg)]"
                        >
                            <img :src="card.image" :alt="card.name" class="h-3/4 w-3/4 max-h-full max-w-full object-contain" />
                        </div>
                    </div>
                </button>
            </div>

            <div v-if="resultOpen" class="rounded-2xl border border-[color:var(--ac-border)] bg-white/90 p-6 text-center shadow-lg">
                <h2 class="text-lg font-bold text-[color:var(--ac-green-dark)]">¡Nivel completado!</h2>
                <p class="mt-2 text-sm text-[color:var(--ac-text-primary)]">
                    Tiempo final: <span class="font-semibold">{{ formatTimeMs(currentTimeMs) }}</span>
                </p>
                <p class="mt-1 text-xs text-[color:var(--ac-text-light)]">
                    Mejor tiempo: {{ formatTimeSeconds(bestTime) }}
                </p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <button
                        type="button"
                        class="rounded-full bg-[color:var(--ac-green-dark)] px-4 py-2 text-sm font-semibold text-white"
                        @click="restart"
                    >
                        Jugar de nuevo
                    </button>
                    <Link
                        v-if="level < 5"
                        :href="route('memory-game.show', level + 1)"
                        class="rounded-full border border-[color:var(--ac-green-dark)] px-4 py-2 text-sm font-semibold text-[color:var(--ac-green-dark)]"
                    >
                        Siguiente nivel
                    </Link>
                </div>
            </div>
        </section>
    </component>
</template>
