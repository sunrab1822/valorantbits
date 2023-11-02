<template>
       <div class="card col-md-4 m-1">
            <img class="card-img " :src="`https://media.valorant-api.com/weaponskins/${uuid}/displayicon.png`">
            <div class="card-img-overlay d-flex align-items-end">
                <h4 class="card-title">{{ name }}</h4>
            </div>
        </div>
</template>

<script setup>
    defineProps(['name', 'uuid'])


</script>

<style>

</style>
