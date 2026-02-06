<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: {
        type: Number,
        default: 0,
    },
    label: String,
})

const emit = defineEmits(['update:modelValue'])

const hours = ref(0)
const minutes = ref(0)

watch([hours, minutes], () => {
    emit('update:modelValue', hours.value * 60 + minutes.value)
})
</script>

<template>
    <div class="duration-field-container">
        <label>{{ label }}</label>

        <div class="duration-field">
            <div class="duration-field-item">
                <input
                    type="number"
                    min="0"
                    v-model.number="hours"
                />
                <label>h</label>
            </div>

            <div class="duration-field-item">
                <input
                    type="number"
                    min="0"
                    max="59"
                    v-model.number="minutes"
                />
                <label>min</label>
            </div>
        </div>
    </div>
</template>
