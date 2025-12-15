<script setup>
import InputField from "../../Components/Inputs/InputField.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const { props } = usePage();
const translations = props.translations;

const form = useForm({
    email: '',
    username: '',
    password: '',
});

const submit = () => {
    form.post('/login', {
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};
</script>

<template>
    <div class="auth-wrapper">
        <div class="image-wrapper">

        </div>

        <div class="content-wrapper">
<!--            TODO: get name from language page-->
            <h2></h2>

            <div class="content">
                <div class="content-info">
                    <h3>{{ translations.auth.login_title }}</h3>
                </div>

                <form class="form-field" @submit.prevent="submit">
                    <InputField
                        v-model="form.email"
                        class="form-field-item"
                        type="email"
                        id="email"
                        name="email"
                        :label="translations.auth.email"
                        :error="form.errors.email"
                    />

                    <InputField
                        v-model="form.password"
                        class="form-field-item"
                        type="password"
                        id="password"
                        name="password"
                        :label="translations.auth.password"
                        :error="form.errors.password"
                    />

                    <button type="submit" class="button full-width primary">
                        {{ translations.auth.login }}
                    </button>
                </form>
            </div>

            <p>
                {{ translations.auth.no_account }}
                <a href="/register">{{ translations.auth.register_title }}</a>
            </p>
        </div>
    </div>
</template>
