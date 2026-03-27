<script setup>
import {ref} from 'vue';
import {Link, usePage} from '@inertiajs/vue3';
import Dropdown from './Dropdown.vue';

const {translations, userRole} = usePage().props;

const props = defineProps({
    household: {type: Object, required: true},
});

const emit = defineEmits(['invite', 'leave']);

const dropdown = ref(null);
</script>

<template>
    <Dropdown ref="dropdown">
        <template #trigger>
            <button class="button">
                <i class="pi pi-ellipsis-v"></i>
            </button>
        </template>

        <li v-if="userRole === 'owner'">
            <button @click="dropdown.close(); emit('invite')">
                <i class="pi pi-user-plus"/>
                Uzaicini lietotāju
            </button>
        </li>
        <li v-if="userRole === 'owner'">
            <Link :href="route('households.edit', household.id)" @click="dropdown?.close()">
                <i class="pi pi-cog"/>
                Iestatījumi
            </Link>
        </li>
        <li>
            <button @click="dropdown?.close(); emit('leave')">
                <i class="pi pi-sign-out"/>
                {{ translations.household.leave }}
            </button>
        </li>
    </Dropdown>
</template>
