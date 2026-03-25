<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage} from "@inertiajs/vue3";
import FolderCard from "../../Components/FolderCard.vue";
import CreateFolderModal from "../../Components/Modals/CreateFolderModal.vue";
import {ref} from "vue";

const {translations, folders} = usePage().props;

const showCreateModal = ref(false);
</script>

<template>
    <MainLayout>
        <header>
            <h1> {{ translations.folders.your_folders }}</h1>

            <button class="button primary" @click="showCreateModal = true">
                <i class="pi pi-plus"/>
            </button>

            <CreateFolderModal
                v-model="showCreateModal"
            />
        </header>

        <div class="grid-container">
            <FolderCard
                v-for="folder in folders"
                :key="folder.id"
                :folder="folder"
                @click="router.visit(route('folders.show', folder.id))"
            />
        </div>
    </MainLayout>
</template>
