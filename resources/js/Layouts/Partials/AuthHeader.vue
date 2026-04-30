<script setup>
import {usePage} from "@inertiajs/vue3";

const {languagePage} = usePage().props;
</script>

<template>
    <header class="site-header auth">
        <a :href="`/${languagePage.slug}/`"><strong>{{languagePage.content.site_name}}</strong></a>
    </header>
</template>
