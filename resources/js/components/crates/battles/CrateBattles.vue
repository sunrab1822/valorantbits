<template>
    <div>
        <div class="d-flex justify-content-end mb-3">
            <router-link class="btn btn-primary create-button" :to="{name: 'create_crate_battles'}">Create Battle</router-link>
        </div>
        <div class="w-100">
            <router-link class="no-style" :to="{name: 'crate_battles_game', params:{id:crate_battle.id}}" v-for="crate_battle in crate_battles">
                <div class="card-battle row mb-2">
                    <div class="col-md-6 d-flex align-items-center battle-crate-list overflow-hidden">
                        <img class="" :src="crate.image" style="max-width: 120px; max-height: 120px;" v-for="crate in crate_battle.crate_list">
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <currency />
                        <div class="fs-5 fw-semibold">{{ crate_battle.price.toBalance(2) }}</div>
                    </div>
                    <div class="col-md-2 d-flex flex-column justify-content-center">
                        <div class="battle_player d-flex" :class="{'mb-2': key != crate_battle.player_list.length - 1}" v-for="(player, key) in crate_battle.player_list">
                            <img class="me-1" style="width: 1.5rem; height: 1.5rem;" :src="player.profile_image" />
                            <div>{{ player.username }}</div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button class="btn btn-primary">View</button>
                    </div>
                </div>
            </router-link>
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
