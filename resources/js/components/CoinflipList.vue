<template>
    <div>
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary create-button align-right" data-bs-toggle="modal" data-bs-target="#coinflipCreateModal" v-if="userStore.isLoggedIn">Create Coinflip</button>
            <!-- <button class="nav-link" type="button" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button> -->
        </div>
        <div>
            <coinflip-card v-for="coinflip in coinflips" :coinflip="JSON.stringify(coinflip)"/>
        </div>
    </div>
    <coinflip-create :isCreate="true" v-if="userStore.isLoggedIn"/>
</template>

<script setup>
    import { ref } from 'vue';
    import axios from 'axios';
    import { useUserStore } from '@stores/user';

    let coinflips = ref([]);
    const userStore = useUserStore();

    window.Echo.channel('Coinflips')
        .listen('.coinflip-created', coinflipCreated)
        .listen('.coinflip-completed', coinflipCompleted);
    getCoinflips();

    function coinflipCreated(data) {
        coinflips.value.push(data.coinflip);
    }

    function coinflipCompleted(data) {
        coinflips.value = coinflips.value.filter(coinflip => data.id !== coinflip.id);
    }

    async function getCoinflips() {
        const response = await axios.get('/api/coinflips');
        coinflips.value = response.data.data;
    }
</script>
