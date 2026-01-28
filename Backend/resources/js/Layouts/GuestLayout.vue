<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="guest-layout">
        <div class="guest-logo">
            <Link href="/">
                <img src="/images/logo.png" alt="ACpedia" class="guest-logo-img" />
            </Link>
        </div>

        <div class="guest-card">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.guest-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: var(--ac-bg-secondary);
    padding: 1.5rem;
}
.guest-logo {
    margin-bottom: 1.5rem;
}
.guest-logo-img {
    height: 80px;
    width: auto;
}
.guest-card {
    width: 100%;
    max-width: 28rem;
    background-color: var(--ac-bg-card);
    border: 2px solid var(--ac-border);
    border-radius: 1rem;
    box-shadow: 0 4px 12px var(--ac-shadow);
    padding: 2rem;
}
</style>
