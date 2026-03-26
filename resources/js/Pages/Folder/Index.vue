<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {router, usePage} from "@inertiajs/vue3";
import FolderCard from "../../Components/FolderCard.vue";
import CreateFolderModal from "../../Components/Modals/CreateFolderModal.vue";
import {computed, ref} from "vue";

const {translations} = usePage().props;
const folders = computed(() => usePage().props.folders);

const showCreateModal = ref(false);
</script>

<template>
    <MainLayout>
        <header class="folder-header">
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
                @click="router.visit(route('folders.show', { user: folder.user.username, folder: folder.name }))"
            />
        </div>
    </MainLayout>
</template>
