<script setup>
import { ref, onMounted } from 'vue';

const isVisible = ref(false);

onMounted(() => {
    // Pequeño delay para que la animación se vea al cargar
    setTimeout(() => {
        isVisible.value = true;
    }, 300);
});
</script>

<template>
    <section class="inicio-section" aria-labelledby="inicio-title">
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
    </section>
</template>

<style scoped src="@/../css/pages/sections/inicio.css"></style>
