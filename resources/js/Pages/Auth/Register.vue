<script setup>
    import { useForm } from '@inertiajs/vue3';
    import InputField from "../../Components/Inputs/InputField.vue";

    const props = defineProps({
        errors: Object
    });

    const form = useForm({
        name: '',
        email: '',
        username: '',
        password: '',
        password_confirmation: ''
    });

    const submit = () => {
        // form.post('/register');
        form.post('/register', {
            onSuccess: () => {
                window.location.href = '/home';
            },
            onError: (errors) => {
                console.log('Validation errors:', errors);
            },
        });
    };

</script>

<template>
    <div class="auth-wrapper">
        <div class="image-wrapper">
            <img src="">
        </div>

        <div class="content-wrapper">
            <h2>Logo/site name from language page</h2>

            <div class="content">
                <div class="content-info">
                    <h3>Sign up</h3>
                </div>

                <form class="form-field" @submit.prevent="submit">
                    <InputField v-model="form.name"
                                type="text"
                                id="full_name"
                                name="name"
                                label="Full name"
                                placeholderValue="Your full name"
                    />
                    <InputField v-model="form.email"
                                type="email"
                                id="email"
                                name="email"
                                label="Email"
                                placeholderValue="Your email"
                    />
                    <InputField v-model="form.username"
                                type="text"
                                id="username"
                                name="username"
                                label="Username"
                                placeholderValue="Your username"
                    />
                    <InputField v-model="form.password"
                                type="password"
                                id="password"
                                name="password"
                                label="Password"
                                placeholderValue="Your password"
                    />
                    <InputField v-model="form.password_confirmation"
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                label="Confirm password"
                                placeholderValue="Confirm your password"
                    />
                    <button type="submit" class="button full-width primary">Register</button>
                </form>
            </div>

            <p>Already have an account? <a href="/login">Log in</a></p>
        </div>
    </div>
</template>
