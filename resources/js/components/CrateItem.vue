<template>
       <div class="card col-md-4 m-1">
            <img class="card-img " :src="image">
            <div class="card-img-overlay d-flex align-items-end">
                <h4 class="card-title">{{ name }}</h4>
            </div>
        </div>
</template>

<script setup>
    defineProps(['name', 'image'])


</script>

<style>

</style>
