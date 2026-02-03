<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    user: Object,
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
})
</script>

<template>
    <div>
        <h1 class="text-xl font-bold">Profile</h1>

        <form @submit.prevent="form.put(route('user-profile-information.update'))">
            <input v-model="form.name" />
            <input v-model="form.email" />

            <button :disabled="form.processing">
                Save
            </button>
        </form>
    </div>
</template>
