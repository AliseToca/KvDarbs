<script setup>
import Rating from "./Rating.vue";

defineProps({
    rating: Number,
    content: {
        type: String,
        default: '',
    },
    username: String,
    created_at: String,
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB');
}
</script>

<template>
    <div class="review">
       <div class="review-header">
           <div class="review-user">
               <img src="">
               <div>
                   <h3>{{ username }}</h3>
                    <Rating :rating="rating"/>
               </div>
           </div>
           <p>{{ formatDate(created_at) }}</p>
       </div>
        <div class="review-body">
            {{ content }}
        </div>
    </div>
</template>

