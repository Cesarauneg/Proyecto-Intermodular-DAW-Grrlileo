import { onMounted, onUnmounted, watch } from 'vue'

const FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'

export function useFocusTrap(containerRef, isActive) {
    let previouslyFocused = null

    const trapFocus = (e) => {
        if (!containerRef.value || e.key !== 'Tab') return

        const focusable = containerRef.value.querySelectorAll(FOCUSABLE_SELECTOR)
        if (focusable.length === 0) return

        const first = focusable[0]
        const last = focusable[focusable.length - 1]

        if (e.shiftKey) {
            if (document.activeElement === first) {
                e.preventDefault()
                last.focus()
            }
        } else {
            if (document.activeElement === last) {
                e.preventDefault()
                first.focus()
            }
        }
    }

    const activate = () => {
        previouslyFocused = document.activeElement
        document.addEventListener('keydown', trapFocus)

        if (containerRef.value) {
            const focusable = containerRef.value.querySelectorAll(FOCUSABLE_SELECTOR)
            if (focusable.length > 0) {
                focusable[0].focus()
            }
        }
    }

    const deactivate = () => {
        document.removeEventListener('keydown', trapFocus)
        if (previouslyFocused && previouslyFocused.focus) {
            previouslyFocused.focus()
        }
        previouslyFocused = null
    }

    watch(isActive, (val) => {
        if (val) activate()
        else deactivate()
    })

    onMounted(() => {
        if (isActive.value) activate()
    })

    onUnmounted(() => {
        deactivate()
    })
}
