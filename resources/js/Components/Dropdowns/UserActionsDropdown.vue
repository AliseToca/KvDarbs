<script setup>
import {computed} from 'vue';
import {Link, usePage} from '@inertiajs/vue3';
import Dropdown from './Dropdown.vue';
import Avatar from '../Avatar.vue';

const page = usePage();
const translations = computed(() => page.props.translations);
const user = computed(() => page.props.user);
const avatarSrc = computed(() => page.props.user?.avatar_src);
</script>

<template>
    <Dropdown :user="user" :avatar-src="avatarSrc">
        <template #trigger>
            <Avatar :avatar-src="avatarSrc" clickable/>
        </template>

        <li>
            <Link :href="route('recipe.my')">
                <i class="pi pi-book"/>
                {{ translations.profile.recipes }}
            </Link>
        </li>
        <li>
            <Link :href="route('folders.index')">
                <i class="pi pi-bookmark"/>
                {{ translations.folders.name }}
            </Link>
        </li>
        <li>
            <Link :href="route('profile.edit')">
                <i class="pi pi-user-edit"/>
                {{ translations.profile.edit_profile }}
            </Link>
        </li>
        <li>
            <Link :href="route('logout')" method="post">
                <i class="pi pi-power-off"/>
                {{ translations.auth.logout }}
            </Link>
        </li>
    </Dropdown>
</template>
