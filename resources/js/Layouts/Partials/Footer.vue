<script setup>
import {usePage} from "@inertiajs/vue3";

const { footerMenu, languagePage, auth} = usePage().props;
</script>

<template>
    <footer class="site-footer">
        <p>This is a footer</p>
        <nav v-if="footerMenu.length && auth.user">
            <ul>
                <li v-for="item in footerMenu" :key="item.id">
                    <a :href="`/${languagePage.slug}/${item.page.slug}`">
                        {{ item.page.name }}
                    </a>
                </li>
            </ul>
        </nav>
    </footer>
</template>
