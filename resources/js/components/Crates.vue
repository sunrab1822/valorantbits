<template>
    <div class="row row-cols-md-5 row-cols-sm-3 g-2">
        <crate v-if="crates.length > 0" v-for="crate in crates" :name="crate.name" :id="crate.id" :image="crate.image" :price="crate.price"/>
    </div>
</template>

<script setup>
    import axios from 'axios';
    import { ref } from 'vue';

    let crates = ref([]);

    loadCrates();

    async function loadCrates() {
        let cratesRequest = await axios.get("/api/crate-list");
        if(cratesRequest.status != 200) return;

        crates.value = cratesRequest.data.data;
    }
</script>
