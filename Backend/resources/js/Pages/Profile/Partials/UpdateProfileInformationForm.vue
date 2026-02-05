<script setup>
    import {
        ref
    } from 'vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import {
        useForm,
        usePage
    } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
    villagers: { type: Array, default: () => [] },
});

    const user = usePage().props.auth.user;

    const showGallery = ref(false);

    const form = useForm({
        name: user.name,
        email: user.email,
        bio: user.bio || '',
        hemisphere: user.hemisphere || '',
        island_name: user.island_name || '',
        island_fruit: user.island_fruit || '',
        avatar: user.avatar || '',
    });
    
const submitForm = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showGallery.value = false;
        },
    });
};

</script>

<template>
    <section>
        <header class="section-header">
            <h2>Información del perfil</h2>
            <p>Personaliza tu identidad y los datos de tu isla.</p>
        </header>

        <form @submit.prevent="submitForm" class="form-body" novalidate>
            <div class="form-group border-b border-gray-100 pb-6">
                <InputLabel value="Tu Avatar" class="mb-2" />

                <div class="flex items-center gap-6">
                    <div class="relative">
                        <img :src="form.avatar ? `/images/villagers/${form.avatar}` : '/images/default_avatar.png'"
                            class="w-20 h-20 rounded-full object-cover border-4 border-green-100 shadow-sm"
                            alt="Avatar actual" />
                        <div
                            class="absolute -bottom-1 -right-1 bg-green-500 w-5 h-5 rounded-full border-2 border-white">
                        </div>
                    </div>

                    <div>
                        <button type="button" @click="showGallery = !showGallery"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ showGallery ? 'Cerrar Galería' : 'Cambiar Avatar' }}
                        </button>
                        <p class="mt-1 text-xs text-gray-500">Haz clic para elegir un vecino</p>
                    </div>
                </div>

                <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                    <div v-if="showGallery" class="mt-4">
                        <div v-if="villagers.length === 0"
                            class="p-4 bg-orange-50 border border-orange-200 rounded-lg text-orange-700 text-sm">
                            ⚠️ No se han encontrado imágenes.
                        </div>

                        <div v-else
                            class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 gap-2 p-3 bg-white rounded-xl border border-gray-200 max-h-60 overflow-y-auto shadow-inner">
                            <div v-for="villager in (props.villagers || [])" :key="villager"
                                @click="form.avatar = villager; showGallery = false"
                                class="cursor-pointer p-1 rounded-full transition-all duration-200 hover:scale-110 flex items-center justify-center border-2"
                                :class="form.avatar === villager ? 'border-green-500 bg-green-50' : 'border-transparent bg-gray-50 hover:bg-gray-100'">
                                <img :src="`/images/villagers/${villager}`" class="w-10 h-10 rounded-full object-cover"
                                    loading="lazy" />
                            </div>
                        </div>
                    </div>
                </Transition>

                <InputError :message="form.errors.avatar" />
            </div>

            <div class="form-group">
                <InputLabel for="name" value="Nombre" />
                <TextInput id="name" type="text" v-model="form.name" class="ac-input w-full" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="form-group">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" v-model="form.email" class="ac-input w-full" />
                <InputError :message="form.errors.email" />
            </div>

            <div class="form-group">
                <InputLabel for="bio" value="Biografía" />
                <TextInput id="bio" type="text" v-model="form.bio" class="ac-input w-full" />
                <InputError :message="form.errors.bio" />
            </div>

            <div class="form-group">
                <InputLabel for="hemisphere" value="Hemisferio" />
                <select id="hemisphere" v-model="form.hemisphere" class="ac-input w-full"
                    style="padding: 0.5rem 0.75rem;">
                    <option value="" disabled>Selecciona tu hemisferio</option>
                    <option value="north">Norte</option>
                    <option value="south">Sur</option>
                </select>
                <InputError :message="form.errors.hemisphere" />
            </div>

            <div class="form-group">
                <InputLabel for="island_name" value="Nombre de la Isla" />
                <TextInput id="island_name" type="text" v-model="form.island_name" class="ac-input w-full" />
                <InputError :message="form.errors.island_name" />
            </div>

            <div class="form-group">
                <InputLabel for="island_fruit" value="Fruta de la Isla" />
                <select id="island_fruit" v-model="form.island_fruit" class="ac-input w-full"
                    style="padding: 0.5rem 0.75rem;">
                    <option value="" disabled>Selecciona la fruta inicial</option>
                    <option value="apples">Manzanas 🍎</option>
                    <option value="pears">Peras 🍐</option>
                    <option value="oranges">Naranjas 🍊</option>
                    <option value="cherries">Cerezas 🍒</option>
                    <option value="peaches">Melocotones 🍑</option>
                </select>
                <InputError :message="form.errors.island_fruit" />
            </div>
<div v-if="form.hasErrors" class="mb-4 p-2 bg-red-50 text-red-600 text-sm rounded border border-red-200">
    Hay errores en el formulario. Por favor, revisa los campos.
</div>
            <div class="form-actions-inline flex items-center gap-4">
                <PrimaryButton :disabled="form.processing" type="submit">
                    Guardar Cambios
                </PrimaryButton>
                
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="saved-text">Guardado.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<style scoped src="@/../css/pages/profile/partials/update-profile-information-form.css"></style>