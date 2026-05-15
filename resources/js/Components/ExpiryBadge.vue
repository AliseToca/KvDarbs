<script setup>
import { computed } from 'vue';

const props = defineProps({
    breakdown: Object,
});

const expiryClass = computed(() => {
    if (!props.breakdown) return 'expiry-badge--beige';
    if (props.breakdown.expired)   return 'expiry-badge--red';
    if (props.breakdown.days <= 3) return 'expiry-badge--orange';
    if (props.breakdown.days <= 7) return 'expiry-badge--yellow';

    return 'expiry-badge--green';
});
</script>

<template>
    <span class="expiry-badge" :class="expiryClass">
        {{ breakdown?.label ?? 'Nav derīguma termiņa' }}
    </span>
</template>
