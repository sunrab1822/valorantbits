<template>
    <a href="">
        <div class="card col m-2">
            <img class="card-img" src="https://placehold.co/600x400">
            <div class="card-img-overlay d-flex align-items-end">
                <h4 class="card-title">{{ name }}</h4>
            </div>
        </div>
    </a>
</template>

<script setup>
    defineProps(["name"]);
</script>
