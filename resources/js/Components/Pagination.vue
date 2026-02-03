<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: {
        type: Array,
        required: true,
    },
})
</script>

<template>
    <nav v-if="links.length > 3" class="pagination">
        <ul class="pagination-list">
            <li
                v-for="(link, index) in links"
                :key="index"
                class="pagination-item"
            >
                <!-- Disabled link -->
                <span
                    v-if="!link.url"
                    class="pagination-link disabled"
                    v-html="link.label"
                />

                <!-- Active / clickable link -->
                <Link
                    v-else
                    class="pagination-link"
                    :class="{ active: link.active }"
                    :href="link.url"
                    preserve-scroll
                    v-html="link.label"
                />
            </li>
        </ul>
    </nav>
</template>
