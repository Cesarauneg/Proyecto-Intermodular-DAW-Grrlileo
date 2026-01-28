<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="auth-layout">
        <nav class="auth-nav">
            <div class="auth-nav-inner">
                <Link href="/" class="auth-nav-logo">
                    <img src="/images/logo.png" alt="ACpedia" class="auth-nav-logo-img" />
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

<style scoped>
.auth-layout {
    min-height: 100vh;
    background-color: var(--ac-bg-primary);
}
.auth-nav {
    background-color: var(--ac-green-dark);
    height: 56px;
    padding: 0 1.5rem;
}
.auth-nav-inner {
    max-width: 1100px;
    margin: 0 auto;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.auth-nav-logo-img {
    height: 36px;
    width: auto;
}
.auth-nav-links {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.auth-nav-link {
    color: #fff;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    border: none;
    background: none;
    cursor: pointer;
    transition: background-color 0.2s;
}
.auth-nav-link:hover {
    background-color: rgba(255,255,255,0.15);
}
.auth-nav-link--danger:hover {
    background-color: rgba(217,79,79,0.3);
}
.auth-header {
    background-color: var(--ac-bg-card);
    border-bottom: 2px solid var(--ac-border);
    padding: 1rem 1.5rem;
}
.auth-header-inner {
    max-width: 1100px;
    margin: 0 auto;
}
.auth-main {
    max-width: 1100px;
    margin: 0 auto;
    padding: 1.5rem;
}
</style>
