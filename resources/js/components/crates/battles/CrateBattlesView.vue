<template>
    <div>
        <div class="spin-wrapper mb-3">
        <div class="spin-selector"></div>
        <div class="spin-wheel d-flex" style="transform: translate3d(7098px, 0px, 0px)">
            <div v-for="(skin, key) in spinItems" class="spin-element flex-column">
                <img :src="skin.image" alt="">
            </div>
        </div>
    </div>
    </div>
</template>

<script setup>
    import axios from 'axios';
    import { ref } from 'vue';

    let crate_battles = ref([]);

    getCrateBattles();

    async function getCrateBattles() {
        let response = await axios.get("/api/crate-battle/list");

        if(!response.data.error) {
            crate_battles.value = response.data.data;
        }
    }
</script>
