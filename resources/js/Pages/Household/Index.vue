<script setup>
import { usePage, router} from '@inertiajs/vue3';
import { ref, reactive } from "vue";
import {route} from 'ziggy-js';
import MainLayout from '../../Layouts/Main.vue';
import Modal from "../../Components/Modal.vue";
import InputField from "../../Components/Inputs/InputField.vue";


const { user, translations, household } = usePage().props;

const isModalOpen = ref(false);

const form = reactive({
    name: '',
    errors: {},
});

function submit() {
    form.errors = {};

    router.post(route('households.store'),
        { name: form.name },
        {
            onSuccess: () => {
                isModalOpen.value = false;
                form.name = '';
            },
            onError: (errors) => {
                form.errors = errors;
            }
        }
    );
}
</script>

<template>
    <MainLayout>
        <button class="button primary" @click="isModalOpen = true">
            {{ translations.button.create }}
        </button>

        <button class="button">
            {{ translations.button.join }}
        </button>

        <Modal v-model="isModalOpen">
            <form class="form-field" @submit.prevent="submit">
                <InputField
                    v-model="form.name"
                    class="form-field-item"
                    type="text"
                    id="name"
                    name="name"
                    :label="translations.household.name"
                    :error="form.errors.name"
                    :max-length="50"
                />

                <button type="submit" class="button full-width primary">
                    {{ translations.button.create }}
                </button>
            </form>
        </Modal>
    </MainLayout>
</template>
