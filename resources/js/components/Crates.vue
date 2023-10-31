<template>
    <div class="row row-cols-4 g-2">
        <crate v-if="crates.length > 0" v-for="crate in crates" :name="crate.name" />
    </div>
</template>

<script setup>
    import axios from 'axios';
    import { ref } from 'vue';

    let crates = ref([]);

    for(let i = 1; i < 36; i++) {
        crates.value.push({name: "Test Case " + i});
    }

    loadCrates();

    async function loadCrates() {
        let cratesRequest = await axios.get("api/crate-list");
        if(cratesRequest.status != 200) return;

        crates.value = cratesRequest.data;
    }
</script>
