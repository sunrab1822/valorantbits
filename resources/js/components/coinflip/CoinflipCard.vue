<template>
    <div class="card col my-2 card-battle bg-dark">
        <div class="card-body text-white d-flex justify-content-between">
            <div class="align-self-center">
                <img class="profile-picture" :src="created_by.profile_image">
            </div>
            <div class="align-self-center">
                <h4>{{ created_by.username }}'s coinflip</h4>
            </div>
            <div class="align-self-center">
                <div class="d-flex">
                    <img src="/storage/radianite.png" class="currency-icon" alt="">
                    <div class="fs-5 fw-semibold">{{ coinflip[coinflip["created_by"] + "_amount"] }}</div>
                </div>
            </div>
            <div class="align-self-center">
                <router-link v-if="coinflip.game_state == 0" class="no-style" :to="{name:'coinflip_game', params:{id:1}}"><button class="btn btn-primary">Join</button></router-link>
                <router-link class="no-style ms-1" :to="{name:'coinflip_game', params:{id:1}}"><button class="btn btn-primary">View</button></router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
    const props = defineProps(["coinflip"])
    const coinflip = JSON.parse(props.coinflip);

    const created_by = coinflip["user_" + coinflip["created_by"]];



</script>
