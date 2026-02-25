<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage, useForm} from "@inertiajs/vue3";
import InputField from "../../Components/Inputs/InputField.vue";
import { ref } from 'vue';
import DeleteAccountModal from "../../Components/Modals/DeleteAccountModal.vue";


const { translations, user } = usePage().props;

// -- Profila informācija --
const profileForm = useForm({
    name: user.name,
    username: user.username,
    email: user.email,
});

const submitProfile = () => {
    profileForm.put(route('user-profile-information.update'),{
        preserveScroll: true,
        errorBag: 'updateProfileInformation',
    });
}

// -- Parole --
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submitPassword = () => {
    passwordForm.put(route('user-password.update'), {
        preserveScroll: true,
        errorBag: 'updatePassword',
        onSuccess: () => passwordForm.reset(),
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
            }
        },
    });
};

// Profila dzēšana
const deleteAccountModal = ref(false);
</script>

<template>
    <MainLayout>
        <h1> {{ translations.auth.profile }} </h1>

        <!-- ---Profila informācija--- -->
        <section class="card">
            <h3> {{ translations.profile.personal_information.title }} </h3>
            <p> {{ translations.profile.personal_information.description }} </p>

            <form class="form-field" @submit.prevent="submitProfile">
                <InputField
                    v-model="profileForm.name"
                    class="form-field-item"
                    :label="translations.auth.name"
                    :error="profileForm.errors.name"
                />

                <InputField
                    v-model="profileForm.username"
                    :label="translations.auth.username"
                    :error="passwordForm.errors.username"
                    :max-length="20"
                />

                <InputField
                    v-model="profileForm.email"
                    type="email"
                    class="form-field-item"
                    :label="translations.auth.email"
                    :error="profileForm.errors.email"
                />

                <button class="button" type="submit" :disabled="profileForm.processing">
                    {{ translations.button.save }}
                </button>
            </form>
        </section>

        <!-- ---Paroles maiņa--- -->
        <section class="card">
            <h3> {{ translations.profile.change_password.title }} </h3>
            <p> {{ translations.profile.change_password.description }} </p>

            <form class="form-field" @submit.prevent="submitPassword">
                <InputField
                    v-model="passwordForm.current_password"
                    type="password"
                    class="form-field-item"
                    :label="translations.auth.current_password "
                    :error="passwordForm.errors.current_password"
                />
                <InputField
                    v-model="passwordForm.password"
                    type="password"
                    class="form-field-item"
                    :label="translations.auth.new_password"
                    :error="passwordForm.errors.password"
                />
                <InputField
                    v-model="passwordForm.password_confirmation"
                    type="password"
                    class="form-field-item"
                    :label="translations.auth.password_confirmation"
                    :error="passwordForm.errors.password_confirmation"
                />

                <button class="button" type="submit" :disabled="passwordForm.processing">
                    {{ translations.button.save }}
                </button>
            </form>
        </section>

        <!-- ---Profila dzēšana--- -->
        <section class="card">
            <h3> {{ translations.profile.delete_account.title }}</h3>
            <p> {{ translations.profile.delete_account.description }} </p>

            <button class="button primary" @click="deleteAccountModal = true">
                {{ translations.button.delete }}
            </button>

            <DeleteAccountModal v-model="deleteAccountModal" />
        </section>
    </MainLayout>
</template>
