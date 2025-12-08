<!--<script>-->
<!--    import AppLayout from '@/Layouts/Main.vue';-->

<!--    export default {-->
<!--        layout: AppLayout,-->
<!--    };-->

<!--</script>-->

<script setup>
    const props = defineProps(['page'])

</script>

<template>
    <div>
        <h1>{{page.name}}</h1>

    </div>
</template>


