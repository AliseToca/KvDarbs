<script setup>
import AuthLayout from '../../Layouts/Auth.vue';
import InputField from "../../Components/Inputs/InputField.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const translations = usePage().props.translations;

const props = defineProps({
    prefillEmail: { type: String, default: '' },
    inviteToken:  { type: String, default: '' },
});

const form = useForm({
    email: props.prefillEmail,
    username: '',
    password: '',
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <AuthLayout>
        <div class="auth-wrapper">
            <div class="content-wrapper">
                <div></div>

                <div class="content">
                    <div class="content-info">
                        <h2>{{ translations.auth.login_title }}</h2>
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
                    <div class="content-info">
                        <a href="/forgot-password" class="link">{{ translations.auth.forgot_password }}</a>
                    </div>

                </div>

                <div class="content-info">
                    <p>
                        {{ translations.auth.no_account }}
                        <a href="/register" class="link-primary">{{ translations.auth.register_title }}</a>
                    </p>
                </div>

            </div>
        </div>
    </AuthLayout>
</template>
