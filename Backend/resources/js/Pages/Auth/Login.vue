<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesión" />

        <div class="form-header">
            <h1>Bienvenido</h1>
            <p>Por favor, identifícate</p>
        </div>

        <div v-if="status" class="form-status">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="form-group">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
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
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="form-group">
                <label class="remember-label">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span>Recordarme</span>
                </label>
            </div>

            <div class="form-actions">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="form-link"
                >
                    ¿Olvidaste la contraseña?
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Iniciar sesión
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
.form-status {
    margin-bottom: 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--ac-green-dark);
}
.form-group {
    margin-bottom: 1rem;
}
.form-group .ac-input {
    margin-top: 0.25rem;
}
.remember-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--ac-text-secondary);
    cursor: pointer;
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
