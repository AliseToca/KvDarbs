<script setup>
import MainLayout from '../../Layouts/Main.vue';
import FolderCard from "../../Components/FolderCard.vue";
import CreateFolderModal from "../../Components/Modals/CreateFolderModal.vue";
import Pagination from "../../Components/Pagination.vue";
import {router, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import NotFound from "../../Components/NotFound.vue";

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
                v-for="folder in folders.data"
                :key="folder.id"
                :folder="folder"
                @click="router.visit(route('folders.show', { user: folder.user.username, folder: folder.name }))"
            />
        </div>

        <NotFound
            v-if="folders.data.length === 0"
            iconClass="pi pi-folder-open"
            :title="translations.folders.not_found.title"
            :message="translations.folders.not_found.message"
        />
        <Pagination :links="folders.links" />
    </MainLayout>
</template>
