<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';

const { user, household } = usePage().props;
</script>

<template>
    <MainLayout>
        <p>{{ user.name }} mājsaimniecība</p>
    </MainLayout>
</template>
