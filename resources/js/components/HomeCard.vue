<template>
    <div class="px-1">
        <div class="game-choice-card py-1">
            <router-link class="styleless-link" :to="{ name: routeName }">
                <h4 class="ps-2">
                    <slot></slot>
                </h4>
                <div class="m-2 game-choice-inner-card d-flex justify-content-center">
                    <img class="card-img" :src="image">
                </div>
            </router-link>
        </div>
    </div>
</template>

<script setup>
    defineProps(["routeName", "image"])
</script>
