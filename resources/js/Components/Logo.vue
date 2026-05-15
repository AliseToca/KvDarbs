<script setup>
defineProps({
    siteName: String,
    siteNameAccent: String,
    href: String,
});
</script>

<template>
    <a class="logo" :href="`/${href}/`">
        <i class="pi pi-home"/>
        <strong>
            {{siteName}}<span class="accent">{{siteNameAccent}}</span>
        </strong>
    </a>
</template>
