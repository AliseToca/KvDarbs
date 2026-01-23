<script setup>
import { watch } from 'vue';

const props = defineProps({
    modelValue: Boolean,
});
const emit = defineEmits(['update:modelValue']);

watch(
    () => props.modelValue,
    (open) => {
        document.body.classList.toggle('no-scroll', open);
    }
);

const close = () => emit('update:modelValue', false);
</script>

<template>
    <Teleport to="#modals">
        <div v-if="modelValue" class="modal-container">
            <div class="modal-overlay" @click="close">
                <div class="modal-inner" @click.stop>
                        <div class="content-wrapper">
                            <button class="button-close" @click="close">
                                <i class="pi pi-times"></i>
                            </button>

                            <slot />
                        </div>
                    </div>
                </div>
            </div>
    </Teleport>
</template>
