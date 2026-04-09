<script setup>
import {computed, ref} from 'vue';

const props = defineProps({
    type: String,
    id: String,
    name: String,
    label: String,
    placeholderValue: String,
    modelValue: [String, Number],
    error: [String, Array],
    maxLength: Number,
    lengthCheck: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const showPassword = ref(false);

const inputType = computed(() => {
    if (props.type === 'password') {
        return showPassword.value ? 'text' : 'password';
    }
    return props.type;
});

const errorList = computed(() => {
    if (!props.error) return [];
    return Array.isArray(props.error) ? props.error : [props.error];
});

const charCount = computed(() => {
    return props.modelValue?.length || 0;
});
</script>

<template>
    <div>
        <label :for="id">{{ label }}</label>

        <div :class="{ 'input-wrapper': type === 'password' }">
            <input
                :type="inputType"
                :id="id"
                :name="name"
                :placeholder="placeholderValue"
                :value="modelValue"
                @input="emit('update:modelValue', $event.target.value)"
                :class="{ 'input-error': errorList.length }"
                :maxlength="maxLength"
            />

            <button
                v-if="type === 'password'"
                type="button"
                class="password-toggle"
                @click="showPassword = !showPassword"
            >
                <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"/>
            </button>
        </div>

        <div v-if="maxLength !== undefined" class="char-count">
            {{ charCount }} / {{ maxLength }}
        </div>

        <ul class="error-container">
            <li v-for="(err, i) in errorList" :key="i" class="error-message">
                {{ err }}
            </li>
        </ul>
    </div>
</template>
