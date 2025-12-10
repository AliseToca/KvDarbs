<script setup>
import { usePage } from '@inertiajs/vue3'
import TempLogoutButton from "../../Components/TempLogoutButton.vue";

const { headerMenu, languagePage, auth} = usePage().props;
console.log('Inertia props:', usePage().props)

</script>

<template>
    <header class="site-header">
        <a :href="`/${languagePage.slug}/`">{{languagePage?.content?.site_name}}</a>

        <nav v-if="headerMenu.length && auth?.user">
            <ul>
                <li v-for="item in headerMenu" :key="item.id">
                    <a :href="`/${languagePage.slug}/${item.page.slug}`">
                        {{ item.page.name }}
                    </a>
                </li>
            </ul>
        </nav>
        <TempLogoutButton class="button primary"></TempLogoutButton>
    </header>
</template>
