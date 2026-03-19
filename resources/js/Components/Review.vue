<script setup>
import Rating from "./Rating.vue";
import Avatar from "./Avatar.vue";
import {usePage} from "@inertiajs/vue3";

const {user} = usePage().props;

const props = defineProps({
    id: Number,
    rating: Number,
    content: {
        type: String,
        default: '',
    },
    username: String,
    avatarSrc: String,
    createdAt: String,
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB');
}
</script>

<template>
    <div class="review">
        <Avatar :avatar-src="avatarSrc" small/>

        <div class="review-content">
            <div class="review-header">
                <div class="review-user-info">
                    <div>
                        <h3>{{ username }}</h3>
                        <p class="date">{{ formatDate(createdAt) }}</p>
                    </div>
                </div>

                <Rating :rating="rating"/>
            </div>

            <div class="review-body">
                {{ content }}
            </div>
        </div>

        <i v-if="username === user.username" class="pi pi-trash delete" @click="$emit('delete')"></i>
    </div>
</template>

