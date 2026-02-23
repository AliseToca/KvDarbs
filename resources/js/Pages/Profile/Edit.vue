<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage, useForm} from "@inertiajs/vue3";
import InputField from "../../Components/Inputs/InputField.vue";

const { translations, user } = usePage().props;

const form = useForm({
    name: user.name,
    username: user.username,
});

const submit = () => {
    form.put(route('profile.update'));
}
</script>

<template>
    <MainLayout>
        <h1> {{ translations.auth.profile }} </h1>

        <form class="form-field" @submit.prevent="submit">
            <InputField
                v-model="form.username"
                :label="translations.auth.username"
                :max-length="20"
            />

            <InputField
                v-model="form.name"
                class="form-field-item"
                :label="translations.auth.name"
            />

            <button class="button" type="submit" :disabled="form.processing">
                {{ translations.button.save }}
            </button>
        </form>
    </MainLayout>
</template>


