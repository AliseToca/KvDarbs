<script setup>
import { router } from '@inertiajs/vue3'

const logout = () => {
    router.post('/logout')
}
</script>

<template>
    <button @click="logout">Logout</button>
</template>

