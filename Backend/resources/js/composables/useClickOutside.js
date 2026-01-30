import { onMounted, onBeforeUnmount } from 'vue';

/**
 * Composable para detectar clicks fuera de un elemento
 * @param {Ref<HTMLElement>} elementRef - Referencia al elemento a monitorear
 * @param {Function} callback - Función a ejecutar cuando se hace click fuera
 */
export function useClickOutside(elementRef, callback) {
    function handler(event) {
        if (elementRef.value && !elementRef.value.contains(event.target)) {
            callback(event);
        }
    }

    onMounted(() => document.addEventListener('click', handler));
    onBeforeUnmount(() => document.removeEventListener('click', handler));
}
