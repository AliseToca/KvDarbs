<script setup>
import AuthLayout from '../../Layouts/Auth.vue';
import InputField from "../../Components/Inputs/InputField.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const translations = usePage().props.translations;

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/reset-password', {
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};
</script>

<template>
    <AuthLayout>
        <div class="auth-wrapper">
            <div class="content-wrapper">
                <div></div>

                <div class="content">
                    <div class="content-info">
                        <h2>{{ translations.auth.reset_password }}</h2>
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
                            :label="translations.auth.new_password"
                            :error="form.errors.password"
                        />

                        <InputField
                            v-model="form.password_confirmation"
                            class="form-field-item"
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            :label="translations.auth.password_confirmation"
                            :error="form.errors.password_confirmation"
                        />

                        <button
                            type="submit"
                            class="button full-width primary"
                            :disabled="form.processing"
                        >
                            {{ translations.auth.reset_password_button }}
                        </button>
                    </form>
                </div>

                <p>
                    {{ translations.auth.have_account }}
                    <a href="/login" class="link">{{ translations.auth.login }}</a>
                </p>
            </div>
        </div>
    </AuthLayout>
</template>
