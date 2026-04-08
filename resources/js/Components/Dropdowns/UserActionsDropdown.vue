<script setup>
import {computed} from 'vue';
import {Link, usePage} from '@inertiajs/vue3';
import Dropdown from './Dropdown.vue';
import Avatar from '../Avatar.vue';
import UserActions from "../UserActions.vue";

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

        <UserActions/>

        <li>
            <Link :href="route('logout')" method="post">
                <i class="pi pi-power-off"/>
                {{ translations.auth.logout }}
            </Link>
        </li>
    </Dropdown>
</template>
