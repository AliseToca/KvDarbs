<script setup>
import {router, useForm, usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import Modal from "./Modal.vue";
import InputField from "../Inputs/InputField.vue";

const { translations } = usePage().props;

const folder = computed(() => usePage().props.folder);

const form = useForm({
    name: folder.name,
});

function submit() {
    form.put(route('folders.update', {folder: folder.value}));
}

function removeFromFolder(recipe) {
    router.delete(route('folders.recipes.destroy', {folder: folder.value, recipe: recipe}));
}
</script>

<template>
    <Modal>
        <template #header>
            <h2>{{ translations.folders.edit_folder }}</h2>
        </template>

        <template #body>
            <form class="form-field" @submit.prevent="submit">
                <InputField
                    v-model="form.name"
                    :maxLength="50"
                    :label="translations.fields.labels.name"
                    :error="form.errors.amount"
                />

                <button class="button" type="submit" :disabled="form.processing">
                    {{ translations.button.save }}
                </button>
            </form>

            <section class="recipes-section">
                <ul v-if="folder.recipes.length > 0" class="folder-list">
                    <li v-for="recipe in folder.recipes" @click="removeFromFolder(recipe)" class="folder-item">
                        <div class="folder-info">
                            <img v-if="recipe.image_src" :src="`/storage/${recipe.image_src}`" class="thumbnail"/>
                            <div v-else class="placeholder"></div>
                            <span class="folder-name">
                            {{ recipe.name }}
                        </span>
                        </div>
                        <button class="button primary">
                            {{ translations.button.remove }}
                        </button>
                    </li>
                </ul>

                <div v-else class="no-folders">
                    <i class="pi pi-folder-open"/>
                    <span>
                        {{ translations.recipe.no_recipes }}
                    </span>
                </div>
            </section>
        </template>
    </Modal>
</template>
