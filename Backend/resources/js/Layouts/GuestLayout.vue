<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="guest-layout">
        <header class="guest-logo">
            <Link href="/">
                <img src="/images/logos/logo.png" alt="Canela`s Desk - Ir al inicio" class="guest-logo-img" />
            </Link>
        </header>

        <main class="guest-card">
            <slot />
        </main>

        <footer class="guest-footer">
            <p>&copy; {{ new Date().getFullYear() }} Canela's Desk. Todos los derechos reservados.</p>
        </footer>
    </div>
</template>

<style scoped src="@/../css/layouts/guest.css"></style>
