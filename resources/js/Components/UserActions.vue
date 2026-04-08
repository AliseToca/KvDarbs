<script setup>
import {Link, usePage} from "@inertiajs/vue3";

const {translations} = usePage().props;
</script>

<template>
    <li>
        <Link :href="route('recipe.my')">
            <i class="pi pi-book"/>
            {{ translations.profile.recipes }}
        </Link>
    </li>
    <li>
        <Link :href="route('folders.index')">
            <i class="pi pi-folder"/>
            {{ translations.folders.name }}
        </Link>
    </li>
    <li>
        <Link :href="route('profile.edit')">
            <i class="pi pi-user-edit"/>
            {{ translations.profile.edit_profile }}
        </Link>
    </li>
</template>
