<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
    modelValue: File | String | null,
    label: String,
    placeholder: {
        type: String,
        default: '/storage/placeholder.jpg',
    },
})

const emit = defineEmits(['update:modelValue'])

const preview = ref(null)

function onFileChange(e) {
    const file = e.target.files[0]

    emit('update:modelValue', file)
    preview.value = URL.createObjectURL(file)
}

watch(
    () => props.modelValue,
    (value) => {
        if (!value) {
            preview.value = null
        } else if (typeof value === 'string') {
            preview.value = `/storage/${value}`
        }
    },
    { immediate: true }
)

const imageSrc = computed(() => {
    return preview.value || props.placeholder
})
</script>

<template>
    <div class="image-upload">
        <label v-if="label">{{ label }}</label>

        <div class="image-upload-container">
            <div class="image-preview">
                <img :src="imageSrc"/>
            </div>

            <input
                type="file"
                accept="image/*"
                @change="onFileChange"
            />
        </div>
    </div>
</template>
