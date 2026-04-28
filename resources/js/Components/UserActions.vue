<script setup>
import {Link, usePage} from "@inertiajs/vue3";

const {translations} = usePage().props;

defineProps({
    showIcons: {
        type: Boolean,
        default: true,
    }
})
</script>

<template>
    <li>
        <Link :href="route('recipe.my')">
            <i v-if="showIcons" class="pi pi-book"/>
            {{ translations.profile.recipes }}
        </Link>
    </li>
    <li>
        <Link :href="route('folders.index')">
            <i v-if="showIcons" class="pi pi-folder"/>
            {{ translations.folders.name }}
        </Link>
    </li>
    <li>
        <Link :href="route('profile.edit')">
            <i v-if="showIcons" class="pi pi-user-edit"/>
            {{ translations.profile.edit_profile }}
        </Link>
    </li>
</template>
