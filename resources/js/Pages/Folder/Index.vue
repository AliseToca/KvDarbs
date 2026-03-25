<script setup>
import MainLayout from '../../Layouts/Main.vue';
import {usePage} from "@inertiajs/vue3";
import FolderCard from "../../Components/FolderCard.vue";

const {translations, folders} = usePage().props;
console.log(folders);
</script>

<template>
    <MainLayout>
        <header>
            <h1> {{ translations.folders.your_folders }}</h1>

            <button class="button primary">
                <i class="pi pi-plus"/>
            </button>
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
