<script setup>
defineProps({
    id: {
        type: String,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    headingLevel: {
        type: String,
        default: 'h2',
        validator: (value) => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(value)
    }
});
</script>

<template>
    <section class="section-placeholder" :aria-labelledby="`${id}-title`">
        <component :is="headingLevel" :id="`${id}-title`">
            {{ title }}
        </component>
        <p>{{ description }}</p>
        <slot />
    </section>
</template>

<style scoped>
.section-placeholder {
    text-align: center;
    padding: 4rem 1rem;
}

.section-placeholder h1,
.section-placeholder h2,
.section-placeholder h3,
.section-placeholder h4,
.section-placeholder h5,
.section-placeholder h6 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--ac-green-dark);
    margin-bottom: 0.5rem;
}

.section-placeholder p {
    color: var(--ac-text-secondary);
    font-size: 1.05rem;
}
</style>
