<script setup>
    import InputField from "../../Components/Inputs/InputField.vue";
    import {useForm} from "@inertiajs/vue3";


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
            <img src="">
        </div>

        <div class="content-wrapper">
            <h2>Logo/site name from language page</h2>

            <div class="content">
                <div class="content-info">
                    <h3>Sign in</h3>
                </div>

                <form class="form-field" @submit.prevent="submit">
                    <InputField v-model="form.email"
                                class="form-field-item"
                                type="email"
                                id="email"
                                name="email"
                                label="Email"
                                placeholderValue="Your email"
                                :error="form.errors.email"
                    />
                    <InputField v-model="form.password"
                                class="form-field-item"
                                type="password"
                                id="password"
                                name="password"
                                label="Password"
                                placeholderValue="Your password"
                                :error="form.errors.password"
                    />

                    <button type="submit" class="button full-width primary">Login</button>
                </form>
            </div>

            <p>Don't have an account? <a href="/register">Register</a></p>
        </div>
    </div>
</template>


