<script setup>
import AuthLayout from '../../Layouts/Auth.vue'
import { useForm, usePage } from '@inertiajs/vue3';
import InputField from "../../Components/Inputs/InputField.vue";

const translations = usePage().props.translations;

const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        onError: (errors) => {
            console.log('Validation errors:', errors);
        },
    });
};
</script>

<template>
    <AuthLayout>
        <div class="auth-wrapper">
            <div class="image-wrapper">
                <img src="../../../assets/images/placeholder.jpg">
            </div>

            <div class="content-wrapper">
                <div></div>

                <div class="content">
                    <div class="content-info">
                        <h3>{{ translations.auth.register_title }}</h3>
                    </div>

                    <form class="form-field" @submit.prevent="submit">
                        <InputField
                            v-model="form.name"
                            class="form-field-item"
                            type="text"
                            id="full_name"
                            name="name"
                            :label="translations.auth.name"
                            :error="form.errors.name"
                        />
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
                            v-model="form.username"
                            class="form-field-item"
                            type="text"
                            id="username"
                            name="username"
                            :label="translations.auth.username"
                            :error="form.errors.username"
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
                        <InputField
                            v-model="form.password_confirmation"
                            class="form-field-item"
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            :label="translations.auth.password_confirmation"
                            :error="form.errors.password_confirmation"
                        />
                        <button type="submit" class="button full-width primary">
                            {{ translations.auth.register }}
                        </button>
                    </form>
                </div>

                <p>
                    {{ translations.auth.have_account }}
                    <a href="/login">{{ translations.auth.login_title }}</a>
                </p>
            </div>
        </div>
    </AuthLayout>
</template>
