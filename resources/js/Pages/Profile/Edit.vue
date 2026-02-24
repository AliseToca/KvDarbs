<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage, useForm} from "@inertiajs/vue3";
import InputField from "../../Components/Inputs/InputField.vue";

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

</script>

<template>
    <MainLayout>
        <h1> {{ translations.auth.profile }} </h1>

        <section>
            <h3> Personīgā informācija</h3>

            <form class="form-field" @submit.prevent="submitProfile">
                <InputField
                    v-model="profileForm.name"
                    class="form-field-item"
                    :label="translations.auth.name"
                />

                <InputField
                    v-model="profileForm.username"
                    :label="translations.auth.username"
                    :max-length="20"
                />

                <InputField
                    v-model="profileForm.email"
                    type="email"
                    :label="translations.auth.email ?? 'Email'"
                    :error="profileForm.errors.email"
                    required
                />

                <button class="button" type="submit" :disabled="profileForm.processing">
                    {{ translations.button.save }}
                </button>
            </form>
        </section>

    </MainLayout>
</template>


