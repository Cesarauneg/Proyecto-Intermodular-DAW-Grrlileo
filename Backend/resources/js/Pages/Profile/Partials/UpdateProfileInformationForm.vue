<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header class="section-header">
            <h2>Información del perfil</h2>
            <p>Actualiza tu nombre y dirección de correo electrónico.</p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="form-body">
            <div class="form-group">
                <InputLabel for="name" value="Nombre" />
                <TextInput
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="form-group">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="verify-text">
                    Tu email no está verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="form-link"
                    >
                        Reenviar email de verificación.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="verify-sent">
                    Se ha enviado un nuevo enlace de verificación.
                </div>
            </div>

            <div class="form-actions-inline">
                <PrimaryButton :disabled="form.processing">Guardar</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="saved-text">
                        Guardado.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<style scoped>
.section-header h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--ac-text-primary);
}
.section-header p {
    margin-top: 0.25rem;
    font-size: 0.85rem;
    color: var(--ac-text-secondary);
}
.form-body {
    margin-top: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.form-group .ac-input {
    margin-top: 0.25rem;
}
.verify-text {
    font-size: 0.85rem;
    color: var(--ac-text-primary);
}
.verify-sent {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--ac-green-dark);
}
.form-link {
    font-size: 0.85rem;
    color: var(--ac-text-light);
    text-decoration: underline;
}
.form-link:hover {
    color: var(--ac-green-dark);
}
.form-actions-inline {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.saved-text {
    font-size: 0.85rem;
    color: var(--ac-text-secondary);
}
</style>
