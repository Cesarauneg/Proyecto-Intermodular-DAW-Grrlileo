<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="auth-layout">
        <nav class="auth-nav" aria-label="Navegación principal">
            <div class="auth-nav-inner">
                <Link href="/" class="auth-nav-logo">
                    <img src="/images/logos/logo.png" alt="ACpedia" class="auth-nav-logo-img" />
                </Link>

                <div class="auth-nav-links">
                    <Link :href="route('profile.edit')" class="auth-nav-link">Perfil</Link>
                    <Link :href="route('logout')" method="post" as="button" class="auth-nav-link auth-nav-link--danger">Cerrar sesión</Link>
                </div>
            </div>
        </nav>

        <header v-if="$slots.header" class="auth-header">
            <div class="auth-header-inner">
                <slot name="header" />
            </div>
        </header>

        <main class="auth-main">
            <slot />
        </main>
    </div>
</template>

<style scoped src="@/../css/layouts/authenticated.css"></style>
