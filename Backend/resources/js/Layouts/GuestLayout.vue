<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="guest-layout">
        <div class="guest-logo">
            <Link href="/">
                <img src="/images/logos/logo.png" alt="ACpedia" class="guest-logo-img" />
            </Link>
        </div>

        <div class="guest-card">
            <slot />
        </div>
    </div>
</template>

<style scoped src="@/../css/layouts/guest.css"></style>
