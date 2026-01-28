<template>
    <button class="ac-btn-danger">
        <slot />
    </button>
</template>

<style scoped>
.ac-btn-danger {
    background-color: var(--ac-danger);
    color: #fff;
    padding: 0.5rem 1.25rem;
    border-radius: 9999px;
    border: 2px solid #c04040;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.1s;
}
.ac-btn-danger:hover {
    background-color: #c04040;
    transform: translateY(-1px);
}
</style>
