<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <header class="section-header">
            <h2>Eliminar cuenta</h2>
            <p>
                Una vez eliminada tu cuenta, todos tus datos se borrarán
                permanentemente. Descarga cualquier información que desees
                conservar antes de proceder.
            </p>
        </header>

        <div style="margin-top: 1rem;">
            <DangerButton @click="confirmUserDeletion">Eliminar cuenta</DangerButton>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="modal-body">
                <h2 class="modal-title">¿Estás seguro de que quieres eliminar tu cuenta?</h2>

                <p class="modal-text">
                    Esta acción es irreversible. Introduce tu contraseña para confirmar.
                </p>

                <div style="margin-top: 1.25rem;">
                    <InputLabel for="password" value="Contraseña" class="sr-only" />
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        placeholder="Contraseña"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="modal-actions">
                    <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>
                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Eliminar cuenta
                    </DangerButton>
                </div>
            </div>
        </Modal>
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
.modal-body {
    padding: 1.5rem;
}
.modal-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--ac-text-primary);
}
.modal-text {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: var(--ac-text-secondary);
}
.modal-actions {
    margin-top: 1.25rem;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}
</style>
