<script setup>
import {router, usePage} from '@inertiajs/vue3'
import Button from "../../Components/Button.vue";

const { headerMenu, languagePage, auth} = usePage().props;
console.log('Inertia props:', usePage().props)

const login = () => {
    router.get('/login')
}

const logout = () => {
    router.post('/logout')
}

</script>

<template>
    <header class="site-header">
        <a :href="`/${languagePage.slug}/`">{{languagePage.content.site_name}}</a>

        <nav v-if="headerMenu.length && auth.user">
            <ul>
                <li v-for="item in headerMenu" :key="item.id">
                    <a :href="`/${languagePage.slug}/${item.page.slug}`">
                        {{ item.page.name }}
                    </a>
                </li>
            </ul>
        </nav>
        <button v-if="auth.user" @click="logout" class="button primary">Logout</button>
        <button v-else @click="login" class="button primary">Login</button>
    </header>
</template>
