<template>
    <div class="card col my-2 card-battle bg-dark">
        <div class="card-body text-white row">
            <div class="col align-self-center">
                <img class="profile-picture" :src="created_by.profile_image">
            </div>
            <div class="col align-self-center">
                <h4>{{ created_by.username }}'s coinflip</h4>
            </div>
            <div class="col align-self-center">
                <div class="d-flex justify-content-end">
                    <currency />
                    <div class="fs-5 fw-semibold">{{ coinflip[coinflip["created_by"] + "_amount"].toBalance(2) }}</div>
                </div>
            </div>
            <div class="col align-self-center d-flex justify-content-end">
                <router-link v-if="coinflip.game_state == 0 && (userStore.user && userStore.user.id != created_by.id)" class="no-style" :to="{name:'coinflip_game', params:{id:coinflip.id}}"><button class="btn btn-primary">Join</button></router-link>
                <router-link class="no-style ms-1" :to="{name:'coinflip_game', params:{id:coinflip.id}}"><button class="btn btn-primary">View</button></router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { useUserStore } from '@stores/user';

    const props = defineProps(["coinflip"])
    const coinflip = JSON.parse(props.coinflip);
    const created_by = coinflip["user_" + coinflip["created_by"]];
    const userStore = useUserStore();

</script>
