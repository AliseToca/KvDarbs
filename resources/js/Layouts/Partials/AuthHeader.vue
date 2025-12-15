<script setup>
import {usePage} from "@inertiajs/vue3";
import Header from "./Header.vue";

const {languagePage} = usePage().props;
</script>

<template>
    <header class="site-header">
        <a :href="`/${languagePage.slug}/`"><strong>{{languagePage.content.site_name}}</strong></a>
    </header>
</template>
