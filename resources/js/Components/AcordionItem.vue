<script setup>
import { ref } from 'vue';

const isOpen = ref(false);

const toggle = () => {
    isOpen.value = !isOpen.value;
}
</script>

<template>
    <div class="accordion-item">
        <button class="accordion-header" @click="toggle" type="button">
            <slot name="header" />

            <i class="pi pi-chevron-down" :class="{'open': isOpen}"></i>
        </button>

        <div v-show="isOpen" class="accordion-body">
            <slot/>
        </div>
    </div>
</template>
