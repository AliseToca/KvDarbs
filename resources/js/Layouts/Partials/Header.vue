<script setup>
import { usePage } from '@inertiajs/vue3'
const page = usePage()

const { headerMenu, languagePage } = usePage().props;
console.log('Inertia props:', page.props)

</script>

<template>
    <header class="site-header">
        <a>{{ languagePage.content.site_name}}</a>

        <nav>
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
    </header>
</template>
