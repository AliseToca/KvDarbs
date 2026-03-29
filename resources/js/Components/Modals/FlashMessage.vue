<script setup>
import {ref, watch, onMounted} from 'vue';
import {usePage} from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('');

const triggerFlash = () => {
    const flash = page.props.flash;
    if (flash?.success) {
        message.value = flash.success;
        type.value = 'success';
        show.value = true;
        setTimeout(() => (show.value = false), 3500);
    } else if (flash?.info) {
        message.value = flash.info;
        type.value = 'info';
        show.value = true;
        setTimeout(() => (show.value = false), 3500);
    } else if (flash?.error) {
        message.value = flash.error;
        type.value = 'error';
        show.value = true;
        setTimeout(() => (show.value = false), 3500);
    }
}

onMounted(triggerFlash);
watch(() => page.props.flash, triggerFlash);
</script>

<template>
    <Transition name="slide">
        <div
            v-if="show"
            :class="`flash-toast flash-${type}`"
        >
            <i :class="{
                'pi pi-check-circle': type === 'success',
                'pi pi-info-circle': type === 'info',
                'pi pi-times-circle': type === 'error',
            }"></i> {{ message }}
        </div>
    </Transition>
</template>
