<script setup>
import { ref, watch, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const show = ref(false)
const message = ref('')

const triggerFlash = () => {
    const flash = page.props.flash
    if (flash?.success) {
        message.value = flash.success
        show.value = true
        setTimeout(() => (show.value = false), 3500)
    }
}

// Fires on initial page load
onMounted(triggerFlash)

// Fires on Inertia navigation / new flash messages
watch(() => page.props.flash, triggerFlash)
</script>

<template>
    <Transition name="slide">
        <div
            v-if="show"
            class="flash-toast flash-success"
        >
            <i class="pi pi-check-circle"></i> {{ message }}
        </div>
    </Transition>
</template>
