<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verificar email" />

        <div class="form-header">
            <h1>Verificar email</h1>
            <p>
                Gracias por registrarte. Verifica tu email haciendo clic en el enlace que te enviamos.
                Si no lo recibiste, te enviaremos otro.
            </p>
        </div>

        <div
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
            v-if="verificationLinkSent"
            role="status"
        >
            Se ha enviado un nuevo enlace de verificación a tu email.
        </div>

        <form @submit.prevent="submit">
            <div class="form-actions" style="justify-content: space-between;">
                <PrimaryButton
                    type="submit"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reenviar email
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="form-link"
                >
                    Cerrar sesión
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
