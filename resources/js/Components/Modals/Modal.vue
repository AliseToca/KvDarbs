<script setup>
import { watch } from 'vue';

const props = defineProps({
    modelValue: Boolean,
});
const emit = defineEmits(['update:modelValue']);

// Novēro izmaiņas loga rādīšanas mainīgajam
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
                    <!-- Paziņojuma loga galvene -->
                    <div class="modal-header">
                        <slot name="header"/>

                        <button class="button-close" @click="close">
                            <i class="pi pi-times"></i>
                        </button>
                    </div>

                    <!-- Paziņojuma loga saturs -->
                    <div class="modal-body">
                        <slot name="body"/>
                    </div>

                    <!-- Paziņojuma loga kājene -->
                    <div class="modal-footer">
                        <slot name= "footer"/>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
