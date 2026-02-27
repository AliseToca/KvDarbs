<script setup>
import { usePage } from '@inertiajs/vue3';

const { user } = usePage().props;

defineProps({
    menu: {
        type: Array,
        required: true
    },
    languagePage: {
        type: Object,
        required: true
    },
})
</script>

<template>
    <!--Navigācijas josla reģistrētiem lietotājiem-->
    <nav v-if="menu.length && user">
        <ul>
            <li v-for="item in menu" :key="item.id">
                <a :href="`/${languagePage.slug}/${item.page.slug}`">
                    {{ item.page.name }}
                </a>
            </li>
        </ul>
    </nav>
</template>
