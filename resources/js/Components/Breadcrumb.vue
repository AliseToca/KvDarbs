<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    items: {
        type: Array,
        required: true,
    }
});
</script>

<template>
    <nav class="breadcrumbs">
        <ul>
            <li v-for="(item, index) in items" :key="index">
                <Link v-if="item.url" :href="item.url">
                    <span>{{ item.name }}</span>
                </Link>
                <span v-else>{{ item.name }}</span>
            </li>
        </ul>
    </nav>
</template>
