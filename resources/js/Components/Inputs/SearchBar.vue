<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placedHolder: String,
})

const search = ref(props.modelValue)

// Nosūta pieprasījumu ar 200ms aizkavi
const performSearch = debounce((value) => {
    router.get(
        window.location.pathname,
        {
            search: value || undefined, page: 1
        },
        {
            preserveState: false,
            preserveScroll: true
        }
    )
}, 200)

watch(search, (value) => {
    performSearch(value)
})
</script>

<template>
    <div class="form-field search-bar">
        <i class="pi pi-search"></i>

        <input
            v-model="search"
            type="search"
            :placeholder="placedHolder"
        />
    </div>
</template>
