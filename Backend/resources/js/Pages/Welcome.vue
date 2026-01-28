<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import InicioSection from './Sections/InicioSection.vue';
import CritterpediaSection from './Sections/CritterpediaSection.vue';
import TemporadaSection from './Sections/TemporadaSection.vue';
import VecinosSection from './Sections/VecinosSection.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const authMenuOpen = ref(false);
const authMenuRef = ref(null);

function toggleAuthMenu() {
    authMenuOpen.value = !authMenuOpen.value;
}

function closeAuthMenu(e) {
    if (authMenuRef.value && !authMenuRef.value.contains(e.target)) {
        authMenuOpen.value = false;
    }
}

onMounted(() => document.addEventListener('click', closeAuthMenu));
onBeforeUnmount(() => document.removeEventListener('click', closeAuthMenu));

const tabs = [
    { key: 'inicio', label: 'Inicio' },
    { key: 'critterpedia', label: 'Critterpedia' },
    { key: 'temporada', label: 'Temporada' },
    { key: 'vecinos', label: 'Vecinos' },
];

const activeTab = ref('inicio');

const sectionComponents = {
    inicio: InicioSection,
    critterpedia: CritterpediaSection,
    temporada: TemporadaSection,
    vecinos: VecinosSection,
};
</script>

<template>
    <Head title="Inicio" />

    <div class="landing">
        <!-- ========== HEADER ========== -->
        <header class="landing-header">
            <div class="header-inner">
                <!-- Logo -->
                <div class="logo">
                    <img src="/images/logo.png" alt="ACpedia" class="logo-img" />
                </div>

                <!-- Auth -->
                <div class="auth-area" ref="authMenuRef">
                    <button class="auth-trigger" @click.stop="toggleAuthMenu">
                        <template v-if="user">
                            Hola, {{ user.name }}
                        </template>
                        <template v-else>
                            Identif&iacute;cate
                        </template>
                        <svg class="chevron" :class="{ open: authMenuOpen }" width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <Transition name="dropdown">
                        <div v-if="authMenuOpen" class="auth-dropdown">
                            <!-- Guest menu -->
                            <template v-if="!user">
                                <Link v-if="canLogin" :href="route('login')" class="dropdown-item">Login</Link>
                                <Link v-if="canRegister" :href="route('register')" class="dropdown-item">Registro</Link>
                            </template>
                            <!-- Authenticated menu -->
                            <template v-else>
                                <Link :href="route('profile.edit')" class="dropdown-item">Perfil</Link>
                                <Link href="#" class="dropdown-item">Colecci&oacute;n</Link>
                                <Link :href="route('logout')" method="post" as="button" class="dropdown-item dropdown-item--danger">Cerrar sesi&oacute;n</Link>
                            </template>
                        </div>
                    </Transition>
                </div>
            </div>
        </header>

        <!-- ========== NAV TABS ========== -->
        <nav class="section-nav">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                class="nav-tab"
                :class="{ active: activeTab === tab.key }"
                @click="activeTab = tab.key"
            >
                {{ tab.label }}
            </button>
        </nav>

        <!-- ========== CONTENT ========== -->
        <main class="content-container">
            <Transition name="fade" mode="out-in">
                <component :is="sectionComponents[activeTab]" :key="activeTab" />
            </Transition>
        </main>
    </div>
</template>

<style scoped>
/* ---- Layout ---- */
.landing {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* ---- Header ---- */
.landing-header {
    background-color: var(--ac-green-dark);
    color: #fff;
    padding: 0 1.5rem;
    height: 56px;
}
.header-inner {
    max-width: 1100px;
    margin: 0 auto;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Logo */
.logo {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 1.35rem;
    font-weight: 800;
    user-select: none;
}
.logo-img {
    height: 40px;
    width: auto;
}

/* Auth area */
.auth-area { position: relative; }
.auth-trigger {
    background: none;
    border: 2px solid rgba(255,255,255,0.35);
    color: #fff;
    padding: 0.35rem 0.9rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    transition: border-color 0.2s;
}
.auth-trigger:hover { border-color: var(--ac-gold-bright); }
.chevron { transition: transform 0.2s; }
.chevron.open { transform: rotate(180deg); }

.auth-dropdown {
    position: absolute;
    right: 0;
    top: calc(100% + 0.5rem);
    background: var(--ac-bg-card);
    border: 2px solid var(--ac-border);
    border-radius: 0.75rem;
    box-shadow: 0 8px 24px var(--ac-shadow);
    min-width: 160px;
    overflow: hidden;
    z-index: 50;
}
.dropdown-item {
    display: block;
    width: 100%;
    text-align: left;
    padding: 0.6rem 1rem;
    color: var(--ac-text-primary);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
    transition: background-color 0.15s;
}
.dropdown-item:hover { background-color: var(--ac-bg-secondary); }
.dropdown-item--danger { color: var(--ac-danger); }
.dropdown-item--danger:hover { background-color: #fdeaea; }

/* Dropdown transition */
.dropdown-enter-active, .dropdown-leave-active { transition: opacity 0.15s, transform 0.15s; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-6px); }

/* ---- Section Nav ---- */
.section-nav {
    display: flex;
    justify-content: center;
    gap: 0.25rem;
    padding: 1rem 1rem 0;
    max-width: 1100px;
    margin: 0 auto;
    width: 100%;
}
.nav-tab {
    background: none;
    border: none;
    padding: 0.55rem 1.4rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--ac-text-light);
    cursor: pointer;
    border-radius: 9999px;
    transition: color 0.2s, background-color 0.2s;
}
.nav-tab:hover { color: var(--ac-green-dark); }
.nav-tab.active {
    color: #fff;
    background-color: var(--ac-green);
}

/* ---- Content ---- */
.content-container {
    flex: 1;
    max-width: 1100px;
    width: 100%;
    margin: 1.5rem auto;
    padding: 0 1.5rem;
}

/* Fade transition */
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
