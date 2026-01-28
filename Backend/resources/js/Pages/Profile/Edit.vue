<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="profile-title">Perfil</h2>
        </template>

        <div class="profile-sections">
            <div class="ac-card">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <div class="ac-card">
                <UpdatePasswordForm />
            </div>

            <div class="ac-card">
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.profile-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--ac-text-primary);
}
.profile-sections {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    max-width: 600px;
}
</style>
