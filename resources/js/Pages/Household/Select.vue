<script setup>
import MainLayout from '../../Layouts/Main.vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import {route} from 'ziggy-js';

const { user, translations, household } = usePage().props;

function createHousehold() {
    router.post(route('households.store'), {
        name: 'My Household'
    });
}
</script>

<template>
    <MainLayout>
        <p>O oh, seems like u don't have a household yet!</p>
        <button class="button primary" @click="createHousehold">Create</button>
        <button class="button">Join</button>

    </MainLayout>
</template>
