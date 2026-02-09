<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue'; 

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    captcha_token: null,
});

onMounted(() => {
    if (!window.grecaptcha) {
        const script = document.createElement('script');
        script.src = "https://www.google.com/recaptcha/api.js";
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    window.onCaptchaSuccess = (token) => {
        form.captcha_token = token;
    };

    window.onCaptchaExpired = () => {
        form.captcha_token = null;
    };
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            
            if (window.grecaptcha) {
                window.grecaptcha.reset();
                form.captcha_token = null;
            }
        },
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

            <div class="form-group flex flex-col items-center justify-center py-2">
                <div 
                    class="g-recaptcha" 
                    data-sitekey="6LccPF8sAAAAAIfVPHiAQ-go4L6_hTVCKcb4HfXG" 
                    data-callback="onCaptchaSuccess"
                    data-expired-callback="onCaptchaExpired"
                ></div>
                <InputError :message="form.errors.captcha_token" />
            </div>

            <div class="form-actions">
                <Link :href="route('login')" class="form-link">
                    ¿Ya tienes cuenta?
                </Link>

                <PrimaryButton
                    type="submit"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing || !form.captcha_token"
                >
                    Registrarse
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped src="@/../css/pages/auth/register.css"></style>