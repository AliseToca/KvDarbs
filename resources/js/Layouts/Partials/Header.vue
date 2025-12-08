<script setup>
import sos from "../../Components/TempLogoutButton.vue";
import { usePage } from '@inertiajs/vue3'
import TempLogoutButton from "../../Components/TempLogoutButton.vue";
const page = usePage()

const { headerMenu, languagePage } = usePage().props;
console.log('Inertia props:', page.props)

</script>

<template>
    <header class="site-header">
        <a>{{ languagePage?.content?.site_name}}</a>

        <nav v-if="headerMenu">
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
        <button class="button primary" >Poga</button>
        <TempLogoutButton></TempLogoutButton>
    </header>
</template>
