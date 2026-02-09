import '../css/app.css';
import '../css/pages/profile/edit.css'; // Añadido para el CSS del perfil
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
        el.style.display = 'block'; // Hacer visible el div#app después del montaje
        return app;
    },
    progress: {
        color: '#4B5563',
    },
});

// Ocultar el div#app hasta que Vue lo monte
const appElement = document.getElementById('app');
if (appElement) {
    appElement.style.display = 'none';
}
