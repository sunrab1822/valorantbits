<template>
    <router-link class="no-style" :to="{ name: 'open_crate', params: { id: id, isAuth: isAuth } }">
        <div class="card col m-2 bg-dark-linear">
            <div class="card-body text-white">
                <div class="">
                    <h4 class="card-title text-center no-wrap">{{ name }}</h4>
                </div>
                <div class="d-flex justify-content-center my-2">
                    <img class="card-img" :src="image" style="max-width: 120px; max-height: 120px;">
                </div>
                <div class="d-flex justify-content-center">
                    <img src="/storage/radianite.png" class="currency-icon" alt="">
                    <div class="fs-5 fw-semibold">{{ price.toBalance(2) }}</div>
                </div>
            </div>
        </div>
    </router-link>
</template>

<script setup>
    defineProps(["name", "id", "image", "price"]);
</script>
