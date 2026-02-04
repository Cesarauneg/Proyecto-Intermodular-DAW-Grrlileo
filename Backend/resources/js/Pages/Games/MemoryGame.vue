<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import DefaultLayout from '@/Layouts/DefaultLayout.vue'; // NEW Import

const props = defineProps({
  villagers: {
    type: Array,
    required: true,
  },
  hourlyMusic: { // NEW Prop
    type: Object,
    default: () => ({})
  },
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const gameStatus = ref('not-started'); // 'not-started', 'memorizing', 'playing', 'won', 'lost'
const difficulty = ref(1); // 1 to 5
const timer = ref(0);
const intervalId = ref(null);
const cards = ref([]);
const flippedCards = reactive([]);
const matchedPairs = ref(0);
const revealedTime = ref(null); // Time cards are revealed for memorization

const gameSettings = {
  1: { pairs: 4, revealTime: 5000 },  // Level 1: 4 pairs (8 cards), 5 seconds reveal
  2: { pairs: 6, revealTime: 4000 },  // Level 2: 6 pairs (12 cards), 4 seconds reveal
  3: { pairs: 8, revealTime: 3000 },  // Level 3: 8 pairs (16 cards), 3 seconds reveal
  4: { pairs: 10, revealTime: 2500 }, // Level 4: 10 pairs (20 cards), 2.5 seconds reveal
  5: { pairs: 10, revealTime: 1500 }, // Level 5: 10 pairs (20 cards), 1.5 seconds reveal (changed pairs from 12 to 10)
};

const currentSettings = computed(() => gameSettings[difficulty.value]);
const totalPairs = computed(() => totalPairs.value * 2);

const initializeGame = () => {
  gameStatus.value = 'not-started';
  timer.value = 0;
  clearInterval(intervalId.value);
  flippedCards.splice(0);
  matchedPairs.value = 0;

  // Select random villagers for the current difficulty
  const shuffledVillagers = [...props.villagers].sort(() => 0.5 - Math.random());
  const selectedVillagers = shuffledVillagers.slice(0, currentSettings.value.pairs);

  // Create pairs
  let newCards = [];
  selectedVillagers.forEach((villager, index) => {
    newCards.push({ id: `card-${index}-a`, villagerId: villager.id, image: villager.image_url, name: villager.name_es, isFlipped: false, isMatched: false });
    newCards.push({ id: `card-${index}-b`, villagerId: villager.id, image: villager.image_url, name: villager.name_es, isFlipped: false, isMatched: false });
  });

  cards.value = newCards.sort(() => 0.5 - Math.random());
  console.log('Generated cards with image URLs:', cards.value.map(c => c.image_url || c.image)); // Log for debugging
};

const startGame = () => {
  initializeGame();
  gameStatus.value = 'memorizing';
  // Reveal all cards for memorization
  cards.value.forEach(card => card.isFlipped = true);
  revealedTime.value = setTimeout(() => {
    cards.value.forEach(card => card.isFlipped = false);
    gameStatus.value = 'playing';
    startTimer();
  }, currentSettings.value.revealTime);
};

const startTimer = () => {
  intervalId.value = setInterval(() => {
    timer.value++;
  }, 1000);
};

const flipCard = (clickedCard) => {
  if (gameStatus.value !== 'playing' || clickedCard.isFlipped || clickedCard.isMatched || flippedCards.length === 2) {
    return;
  }

  clickedCard.isFlipped = true;
  flippedCards.push(clickedCard);

  if (flippedCards.length === 2) {
    setTimeout(() => {
      const [card1, card2] = flippedCards;
      if (card1.villagerId === card2.villagerId) {
        card1.isMatched = true;
        card2.isMatched = true;
        matchedPairs.value++;
        if (matchedPairs.value === currentSettings.value.pairs) { // Changed totalPairs.value to currentSettings.value.pairs
          endGame('won');
        }
      } else {
        card1.isFlipped = false;
        card2.isFlipped = false;
      }
      flippedCards.splice(0);
    }, 1000);
  }
};

const endGame = async (status) => {
  gameStatus.value = status;
  clearInterval(intervalId.value);
  clearTimeout(revealedTime.value);

  if (status === 'won' && isAuthenticated.value) {
    try {
      await axios.post(route('games.memory.save'), {
        level: difficulty.value,
        completion_time_seconds: timer.value,
      });
      console.log('Score saved!');
    } catch (error) {
      console.error('Error saving score:', error);
    }
  }
};

onMounted(() => {
  initializeGame();
});

const formatTime = (seconds) => {
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
};
</script>

<template>
      <DefaultLayout
        title="Memory Game"
        :can-login="false"
        :can-register="false"
        :hourly-music="hourlyMusic"
      >    <div class="flex flex-col items-center justify-center py-12 flex-1"> <!-- Added flex-1 to fill space -->
      <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-4xl">
        <h1 class="text-4xl font-extrabold text-center text-gray-800 mb-6">Memory Game de Vecinos</h1>

        <!-- Game Controls -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-4">
          <div class="flex items-center gap-2">
            <label for="difficulty" class="text-gray-700 font-semibold">Dificultad:</label>
            <select id="difficulty" v-model="difficulty" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
              <option v-for="level in 5" :key="level" :value="level">Nivel {{ level }}</option>
            </select>
          </div>

          <div class="text-2xl font-bold text-gray-900">Tiempo: {{ formatTime(timer) }}</div>

          <button
            @click="startGame"
            :disabled="gameStatus === 'memorizing'"
            class="px-6 py-3 bg-green-600 text-white font-bold rounded-lg shadow hover:bg-green-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ gameStatus === 'playing' ? 'Reiniciar' : 'Empezar Juego' }}
          </button>
        </div>

        <!-- Game Board -->
        <div
          v-if="gameStatus !== 'not-started'"
          class="grid gap-4 justify-center grid-cols-[repeat(auto-fit,minmax(8rem,1fr))]"
        >
          <div
            v-for="card in cards"
            :key="card.id"
            @click="flipCard(card)"
            :class="[
              'relative w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-lg cursor-pointer transform-gpu transition-transform duration-500 preserve-3d',
              { 'rotate-y-180': card.isFlipped || card.isMatched }
            ]"
          >
            <!-- Card Front (Image) -->
            <div class="absolute inset-0 backface-hidden bg-white rounded-lg shadow-md flex items-center justify-center p-2">
              <img :src="card.image_url" :alt="card.name" class="max-w-full max-h-full object-contain">
            </div>

            <!-- Card Back (Question Mark / Logo) -->
            <div class="absolute inset-0 backface-hidden bg-blue-500 rounded-lg shadow-md flex items-center justify-center rotate-y-180">
              <img src="/images/logos/favicon.png" alt="Card Back" class="w-1/2 h-1/2 object-contain filter invert opacity-75">
            </div>
          </div>
        </div>

        <!-- Game Status Message -->
        <div v-if="gameStatus === 'won'" class="mt-8 text-center text-green-700 text-3xl font-bold animate-bounce">
          ¡Felicidades! ¡Has ganado en {{ formatTime(timer) }}!
        </div>
        <div v-if="gameStatus === 'lost'" class="mt-8 text-center text-red-700 text-3xl font-bold">
          ¡Tiempo terminado! ¡Inténtalo de nuevo!
        </div>

        <div v-if="gameStatus === 'memorizing'" class="mt-8 text-center text-blue-700 text-xl font-semibold">
          Memoriza las cartas... ({{ currentSettings.revealTime / 1000 }} segundos)
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<style scoped>
/* Flip Card Effect */
.preserve-3d {
  transform-style: preserve-3d;
}

.backface-hidden {
  backface-visibility: hidden;
}

.rotate-y-180 {
  transform: rotateY(180deg);
}
</style>