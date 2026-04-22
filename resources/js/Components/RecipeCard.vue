<script setup>
import {Link} from "@inertiajs/vue3";
import {ref} from "vue";
import FormatTime from "./FormatTime.vue";
import {HugeiconsIcon} from '@hugeicons/vue';
import {HamIcon} from '@hugeicons/core-free-icons';
import AddToFolderModal from "../Components/Modals/AddToFolderModal.vue";

const props = defineProps({
    id: Number,
    url: String,
    slug: String,
    name: String,
    imageSrc: String,
    rating: {
        type: Number,
        default: 0,
    },
    reviews_count: Number,
    time_minutes: Number,
    missing_products_count: Number,
    available_products_count: Number,
    total_products_count: Number,
    compatibility: Number,
    servings: Number,
    show_missing_products: {
        type: Boolean,
        default: true,
    },
});

const isConfirmAddToFolderOpen = ref(false);
</script>

<template>
    <Link
        :href="url"
    >
        <div class="recipe-card-wrapper">
            <div class="recipe-card">
                <div>
                    <img :src="imageSrc ? `/storage/${imageSrc}` : '/storage/placeholder.jpeg'">

                    <button
                        @click="(e) => { e.stopPropagation(); e.preventDefault(); isConfirmAddToFolderOpen = true;}"
                        class="recipe-bookmark-icon"
                    >
                        <i class="pi pi-bookmark"/>
                    </button>

                    <AddToFolderModal
                        v-model="isConfirmAddToFolderOpen"
                        :recipe="id"
                    />

                    <h2 class="recipe-title">{{ name }}</h2>
                </div>

                <div>
                    <div class="recipe-card-item">
                    <span>
                        <i class="pi pi-star-fill recipe-rating"></i>
                        <span class="recipe-rating" >{{ rating? rating.toFixed(1) : '0.0' }}</span>
                        ({{reviews_count}})
                    </span>
                        <span>
                        <i class="pi pi-clock"/>
                        <FormatTime :timeMinutes="time_minutes"/>
                    </span>
                        <span>
                        <i class="pi pi-users"/>
                        {{ servings }}
                    </span>
                    </div>

                    <div v-show="show_missing_products" class="ingredient-compatibility">
                        <div class="ingredient-compatibility-status">
                            <HugeiconsIcon
                                :icon="HamIcon"
                                :size="20"
                                color="currentColor"
                                :stroke-width="1.7"
                            />
                            <span>{{ available_products_count }} / {{ total_products_count }}</span>
                        </div>

                        <div class="compatibility-bar">
                            <div
                                class="compatibility-bar-fill"
                                :style="{ width: compatibility + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Link>
</template>
