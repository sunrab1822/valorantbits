<template>
    <div>
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary create-button align-right" data-bs-toggle="modal" data-bs-target="#coinflipCreateModal">Create Game</button>
            <!-- <button class="nav-link" type="button" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button> -->
        </div>
        <div>
            <coinflip-card v-for="coinflip in coinflips" :coinflip="JSON.stringify(coinflip)"/>
        </div>
    </div>
    <coinflip-create :isCreate="true"/>
</template>

<script setup>
    import { ref } from 'vue';
    import axios from 'axios';

    let coinflips = ref([]);

    window.Echo.channel('CoinflipList').listen('.coinflip-created', coinflipCreated);
    getCoinflips();

    function coinflipCreated(data) {
        coinflips.value.push(data);
    }

    async function getCoinflips() {
        const response = await axios.get('/api/coinflips');
        coinflips.value = response.data.data;
    }
</script>
