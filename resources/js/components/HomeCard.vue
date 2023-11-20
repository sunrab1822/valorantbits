<template>
    <div class="game-choice-card">
        <router-link class="styleless-link" :to="{ name: routeName }">
            <div class="m-2 game-choice-inner-card d-flex justify-content-center">
                <img class="card-img" :src="image">
            </div>
            <h4 class="text-center">
                <slot></slot>
            </h4>
        </router-link>
    </div>
</template>

<script setup>
    defineProps(["routeName", "image"])
</script>
