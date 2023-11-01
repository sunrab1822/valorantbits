<template>
    <div class="row row-cols-4 g-2">
        <crate v-if="crates.length > 0" v-for="crate in crates" :name="crate.name" />
    </div>
</template>

<script setup>
    import axios from 'axios';
    import { ref } from 'vue';

    let crates = ref([]);

    loadCrates();

    async function loadCrates() {
        let cratesRequest = await axios.get("api/crate-list");
        if(cratesRequest.status != 200) return;

        crates.value = cratesRequest.data.data;
        console.log(cratesRequest.data)
    }
</script>
