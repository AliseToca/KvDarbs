<script setup>
import { ref, onMounted, onUnmounted, defineExpose } from 'vue';

const props = defineProps({
    // Neobligāta lietotāja informācija - tiek renderēta tikai tad, ja tā ir norādīta
    user: {
        type: Object,
        default: null,
    },
    avatarSrc: {
        type: String,
        default: null,
    },
});

const isOpen = ref(false);
const dropdownRef = ref(null);

const toggle = () => (isOpen.value = !isOpen.value);
const close = () => (isOpen.value = false);

const closeOnOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        close();
    }
}

defineExpose({ close: () => isOpen.value = false });

onMounted(() => document.addEventListener('click', closeOnOutside));
onUnmounted(() => document.removeEventListener('click', closeOnOutside));
</script>

<template>
    <div class="dropdown-wrapper" ref="dropdownRef">
        <!-- Atvers izvēlni -->
        <div @click="toggle" class="dropdown-trigger">
            <slot name="trigger" />
        </div>

        <ul v-if="isOpen" class="dropdown">
            <!-- Lietotāja informācijas bloks - tiek rādīts tikai tad, ja tiek nodots lietotāja parametrs -->
            <li v-if="user" class="user-info">
                <p><strong>{{ user.name }}</strong></p>
                <p>@{{ user.username }}</p>
            </li>

            <!-- Visi izvēlnes elementi iet šeit -->
            <slot />
        </ul>
    </div>
</template>
