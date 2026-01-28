<script setup>
</script>

<template>
    <div class="inicio-section">
        <div class="hero">
            <h1>Bienvenido a tu isla</h1>
            <p>Explora el mundo de Animal Crossing: descubre criaturas, vecinos, arte y mucho mas.</p>
        </div>
    </div>
</template>

<style scoped>
.inicio-section {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    text-align: center;
}
.hero h1 {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--ac-brown);
    margin-bottom: 0.75rem;
}
.hero p {
    font-size: 1.15rem;
    color: var(--ac-text-secondary);
    max-width: 500px;
    margin: 0 auto;
}
</style>
