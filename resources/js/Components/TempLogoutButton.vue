<script setup>
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';

const logout = async () => {
    try {
        await axios.post('/logout'); // send POST to Fortify logout route
        window.location.href = '/register'; // redirect to registration page
    } catch (error) {
        console.error('Logout failed', error);
    }
};
</script>

<template>
    <button @click="logout">Logout</button>
</template>

