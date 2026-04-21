<script setup>
import Modal from "./Modal.vue";
import {usePage, router} from "@inertiajs/vue3";

const {folders, translations, recipe} = usePage().props;

function saveToFolder(folder) {
    router.post(route('folders.recipes.store', {folder: folder, recipe: recipe}));
}
</script>

<template>
    <Modal innerClass="add-to-folder-modal">
        <template #header>
            <h2>{{ translations.folders.add_to_folder }}</h2>
        </template>

        <template #body>
            <ul v-if="folders.length > 0" class="folder-list">
                <li v-for="folder in folders" :key="folder.id" @click="saveToFolder(folder)" class="folder-item">
                    <div class="folder-info">
                        <img v-if="folder.thumbnail" :src="`/storage/${folder.thumbnail}`" class="thumbnail"/>
                        <div v-else class="placeholder"></div>
                        <span class="folder-name">
                            {{ folder.name }}
                        </span>
                    </div>
                    <button class="button primary">
                        {{ translations.button.save }}
                    </button>
                </li>
            </ul>

            <div v-else class="no-folders">
                <i class="pi pi-folder-open"/>
                <span>
                    {{ translations.folders.no_folders }}
                </span>
            </div>
        </template>
    </Modal>
</template>
