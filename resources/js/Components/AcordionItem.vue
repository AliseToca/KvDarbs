<script setup>
import {ref} from 'vue';

const props = defineProps({
    defaultOpen: {
        type: Boolean,
        default: false,
    }
});

const isOpen = ref(props.defaultOpen);
const toggle = () => {
    isOpen.value = !isOpen.value;
}
</script>

<template>
    <div class="accordion-item">
        <button class="accordion-header" @click="toggle" type="button">
            <slot name="header"/>
            <i class="pi pi-chevron-down" :class="{'open': isOpen}"></i>
        </button>

        <Transition name="accordion">
            <div v-if="isOpen" class="accordion-body">
                <slot/>
            </div>
        </Transition>
    </div>
</template>
