<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="section-header">
            <h2>Actualizar contraseña</h2>
            <p>Usa una contraseña larga y segura para proteger tu cuenta.</p>
        </header>

        <form @submit.prevent="updatePassword" class="form-body">
            <div class="form-group">
                <InputLabel for="current_password" value="Contraseña actual" />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" />
            </div>

            <div class="form-group">
                <InputLabel for="password" value="Nueva contraseña" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="form-group">
                <InputLabel for="password_confirmation" value="Confirmar contraseña" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" />
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
