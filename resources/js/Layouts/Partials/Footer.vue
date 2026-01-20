<script setup>
import {usePage} from "@inertiajs/vue3";
import NavBar from "../../Components/NavBar.vue";

const { footerMenu, languagePage, auth} = usePage().props;
</script>

<template>
    <footer class="site-footer">
        <div class="container">
            <p>This is a footer</p>
            <NavBar
                :menu="footerMenu"
                :languagePage="languagePage"
                :auth="auth"/>
        </div>
    </footer>
</template>
