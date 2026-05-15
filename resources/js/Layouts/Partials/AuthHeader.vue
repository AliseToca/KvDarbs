<script setup>
import {usePage} from "@inertiajs/vue3";
import Logo from "../../Components/Logo.vue";
const {languagePage} = usePage().props;
</script>

<template>
    <header class="site-header auth">
        <Logo
            :siteName=languagePage.content.site_name
            :siteNameAccent=languagePage.content.site_name_accent
            :href=languagePage.slug
        />
    </header>
</template>
