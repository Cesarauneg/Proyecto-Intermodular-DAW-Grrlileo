<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import DeleteUserForm from './Partials/DeleteUserForm.vue';
    import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
    import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
    import '@/../css/pages/profile/edit.css';
    import {
        Head
    } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    villagers: {
        type: Array,
        default: () => []
    },
});
</script>

<template>
    <Head title="Perfil" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="profile-title">Perfil</h1>
        </template>

        <div class="profile-sections">
            <div class="ac-card">
                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status"
                    :villagers="villagers" class="max-w-xl" />
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

<!-- <style scoped src="@/../css/pages/profile/edit.css"></style> -->
