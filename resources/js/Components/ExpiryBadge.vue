<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
});

const expiryClass = computed(() => {
    const breakdown = props.product.expiryBreakdown;

    if (!breakdown) return 'expiry-badge--green';
    if (breakdown.expired)  return 'expiry-badge--red';
    if (breakdown.days <= 0) return 'expiry-badge--orange';
    if (breakdown.days <= 3) return 'expiry-badge--orange';
    if (breakdown.days <= 7) return 'expiry-badge--yellow';

    return 'expiry-badge--green';
});
</script>

<template>
    <span class="expiry-badge" :class="expiryClass">
        {{ product.expiryBreakdown.label }}
    </span>
</template>
