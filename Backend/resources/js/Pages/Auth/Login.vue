<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

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
    // captcha_token: null, // Temporarily disabled 
});

// onMounted(() => { // Temporarily disabled
//     if (!window.grecaptcha) {
//         const script = document.createElement('script');
//         script.src = "https://www.google.com/recaptcha/api.js";
//         script.async = true;
//         script.defer = true;
//         document.head.appendChild(script);
//     }

//     window.onCaptchaSuccess = (token) => {
//         form.captcha_token = token;
//     };

//     window.onCaptchaExpired = () => {
//         form.captcha_token = null;
//     };
// });

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            // if (window.grecaptcha) { // Temporarily disabled
            //     window.grecaptcha.reset();
            //     form.captcha_token = null;
            // }
        },
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
                <div class="remember-label">
                    <Checkbox id="remember" name="remember" v-model:checked="form.remember" />
                    <label for="remember">Recordarme</label>
                </div>
            </div>

                        <!-- <div class="form-group flex flex-col items-center justify-center py-2"> // Temporarily disabled
                            <div
                                class="g-recaptcha"
                                data-sitekey="6LccPF8sAAAAAIfVPHiAQ-go4L6_hTVCKcb4HfXG"
                                data-callback="onCaptchaSuccess"
                                data-expired-callback="onCaptchaExpired"
                            ></div>
                            <InputError :message="form.errors.captcha_token" />
                        </div> -->
            <div class="form-actions">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="form-link"
                >
                    ¿Olvidaste la contraseña?
                </Link>

                <PrimaryButton
                    type="submit"
                    :class="{ 'opacity-25': form.processing }"
                                         :disabled="form.processing"                >
                    Iniciar sesión
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped src="@/../css/pages/auth/login.css"></style>