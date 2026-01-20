<script setup>
defineProps({
    menu: {
        type: Array,
        required: true
    },
    languagePage: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <!--Navigācijas josla reģistrētiem lietotājiem-->
    <nav v-if="menu.length && auth.user">
        <ul>
            <li v-for="item in menu" :key="item.id">
                <a :href="`/${languagePage.slug}/${item.page.slug}`">
                    {{ item.page.name }}
                </a>
            </li>
        </ul>
    </nav>
</template>
