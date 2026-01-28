<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registro" />

        <div class="form-header">
            <h1>Crear cuenta</h1>
            <p>Únete a la comunidad</p>
        </div>

        <form @submit.prevent="submit">
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

            <div class="form-group">
                <InputLabel for="password" value="Contraseña" />
                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="form-group">
                <InputLabel for="password_confirmation" value="Confirmar contraseña" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <div class="form-actions">
                <Link :href="route('login')" class="form-link">
                    ¿Ya tienes cuenta?
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Registrarse
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.form-header {
    text-align: center;
    margin-bottom: 1.5rem;
}
.form-header h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--ac-brown);
}
.form-header p {
    color: var(--ac-text-secondary);
    font-size: 0.9rem;
}
.form-group {
    margin-bottom: 1rem;
}
.form-group .ac-input {
    margin-top: 0.25rem;
}
.form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.25rem;
}
.form-link {
    font-size: 0.8rem;
    color: var(--ac-text-light);
    text-decoration: underline;
    transition: color 0.2s;
}
.form-link:hover {
    color: var(--ac-green-dark);
}
</style>
