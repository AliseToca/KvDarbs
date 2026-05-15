<script setup>
import AuthLayout from '../../Layouts/Auth.vue';
import InputField from "../../Components/Inputs/InputField.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const translations = usePage().props.translations;

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password', {
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
                        <h2>{{ translations.auth.forgot_password }}</h2>
                        <p v-if="!form.wasSuccessful" class="description">
                            {{ translations.auth.forgot_password_hint }}
                        </p>
                    </div>

                    <div v-if="form.wasSuccessful" class="success-message">
                        {{ translations.auth.forgot_password_success }}
                    </div>

                    <form v-else class="form-field" @submit.prevent="submit">
                        <InputField
                            v-model="form.email"
                            class="form-field-item"
                            type="email"
                            id="email"
                            name="email"
                            :label="translations.auth.email"
                            :error="form.errors.email"
                        />

                        <button
                            type="submit"
                            class="button full-width primary"
                            :disabled="form.processing"
                        >
                            {{ translations.auth.forgot_password_button }}
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
