<script setup>
import { usePage } from '@inertiajs/vue3'
import TempLogoutButton from "../../Components/TempLogoutButton.vue";

const { headerMenu, languagePage, auth } = usePage().props;
console.log('Inertia props:', usePage().props)

</script>

<template>
    <header class="site-header">
        <a>{{ languagePage?.content?.site_name}}</a>

        <nav v-if="headerMenu && auth">
            <ul>
                <li
                    v-for="item in headerMenu.items"
                    :key="item.id"
                >
                    <a
                        :href="`/${languagePage.slug}/${item.page.slug}`"
                    >
                        {{item.page.name}}
                    </a>
                </li>
            </ul>
        </nav>
        <TempLogoutButton class="button primary"></TempLogoutButton>
    </header>
</template>
