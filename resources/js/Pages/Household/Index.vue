<script setup>
import { usePage, useForm } from '@inertiajs/vue3';
import { ref } from "vue";
import { route } from 'ziggy-js';
import MainLayout from '../../Layouts/Main.vue';
import Modal from "../../Components/Modals/Modal.vue";
import InputField from "../../Components/Inputs/InputField.vue";

const { translations } = usePage().props;

const isModalOpen = ref(false);

const form = useForm({
    name: '',
});

function submit() {
    form.post(route('households.store'), {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        },
    });
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
                <template #header>
                    <h2>
                        {{ translations.household.ur_household}}
                    </h2>
                </template>

                <template #body>
                    <form class="form-field" id="create-household-form" @submit.prevent="submit">
                        <InputField
                            v-model="form.name"
                            class="form-field-item"
                            type="text"
                            id="name"
                            name="name"
                            :label="translations.fields.labels.name"
                            :error="form.errors.name"
                            :max-length="30"
                        />
                    </form>
                </template>

                <template #footer>
                    <button form="create-household-form" type="submit" class="button full-width primary" :disabled="form.processing">
                        {{ translations.button.create }}
                    </button>
                </template>
        </Modal>
    </MainLayout>
</template>
