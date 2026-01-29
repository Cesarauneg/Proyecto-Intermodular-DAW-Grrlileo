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
                    <img src="/images/logos/logo.png" alt="ACpedia" class="logo-img" />
                </div>

                <!-- Auth -->
                <div class="auth-area" ref="authMenuRef">
                    <button
                        class="auth-trigger"
                        @click.stop="toggleAuthMenu"
                        :aria-expanded="authMenuOpen"
                        aria-haspopup="true"
                    >
                        <template v-if="user">
                            Hola, {{ user.name }}
                        </template>
                        <template v-else>
                            Identif&iacute;cate
                        </template>
                        <svg class="chevron" :class="{ open: authMenuOpen }" width="14" height="14" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <Transition name="dropdown">
                        <div v-if="authMenuOpen" class="auth-dropdown" role="menu">
                            <!-- Guest menu -->
                            <template v-if="!user">
                                <Link v-if="canLogin" :href="route('login')" class="dropdown-item" role="menuitem">Login</Link>
                                <Link v-if="canRegister" :href="route('register')" class="dropdown-item" role="menuitem">Registro</Link>
                            </template>
                            <!-- Authenticated menu -->
                            <template v-else>
                                <Link :href="route('profile.edit')" class="dropdown-item" role="menuitem">Perfil</Link>
                                <Link href="#" class="dropdown-item" role="menuitem">Colecci&oacute;n</Link>
                                <Link :href="route('logout')" method="post" as="button" class="dropdown-item dropdown-item--danger" role="menuitem">Cerrar sesi&oacute;n</Link>
                            </template>
                        </div>
                    </Transition>
                </div>
            </div>
        </header>

        <!-- ========== NAV TABS ========== -->
        <nav class="section-nav" role="tablist" aria-label="Secciones del sitio">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                class="nav-tab"
                :class="{ active: activeTab === tab.key }"
                @click="activeTab = tab.key"
                role="tab"
                :aria-selected="activeTab === tab.key"
                :aria-controls="`tabpanel-${tab.key}`"
                :id="`tab-${tab.key}`"
            >
                {{ tab.label }}
            </button>
        </nav>

        <!-- ========== CONTENT ========== -->
        <main class="content-container">
            <Transition name="fade" mode="out-in">
                <div
                    :key="activeTab"
                    role="tabpanel"
                    :id="`tabpanel-${activeTab}`"
                    :aria-labelledby="`tab-${activeTab}`"
                >
                    <component :is="sectionComponents[activeTab]" />
                </div>
            </Transition>
        </main>

        <!-- ========== MASCOT ========== -->
        <img src="/images/logos/totakeke.gif" alt="" class="mascot-gif" />

        <!-- ========== FOOTER ========== -->
        <footer class="landing-footer">
            <p>&copy; {{ new Date().getFullYear() }} Canela's Desk. Todos los derechos reservados.</p>
        </footer>
    </div>
</template>

<style scoped src="@/../css/pages/welcome.css"></style>
