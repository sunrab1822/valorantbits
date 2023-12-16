<template>
    <div>
        <div class="d-flex justify-content-end mb-3">
            <router-link class="btn btn-primary create-button" :to="{name: 'create_crate_battles'}" v-if="userStore.isLoggedIn">Create Battle</router-link>
        </div>
        <div class="w-100">
            <router-link class="no-style" :to="{name: 'crate_battles_game', params:{id:crate_battle.id}}" v-for="(crate_battle, index) in crate_battles">
                <div class="card-battle row mb-2">
                    <div class="col-md-5 d-flex align-items-center battle-crate-list overflow-hidden">
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
                    <div class="col-md-1 d-flex justify-content-center align-items-center text-center">
                        <div class="fs-6-1 text-uppercase mt-1 ls-0-5">{{ getBattleType(index) }}</div>
                    </div>
                    <div class="col-md-2 d-flex justify-content-center align-items-center">
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
    import { useUserStore } from '@stores/user';

    let crate_battles = ref([]);
    const userStore = useUserStore();

    getCrateBattles();

    window.Echo.channel("CrateBattles")
        .listen(".battle-created", battleCreated)
        .listen(".battle-completed", battleCompleted);

    function getBattleType(index) {
        if(crate_battles.value[index].is_normal) {
            if(crate_battles.value[index].is_crazy) {
                return "crazy";
            }
            return "normal";
        }
        if(crate_battles.value[index].is_terminal) {
            return (crate_battles.value[index].is_crazy ? "crazy " : "") + "terminal";
        }
        if(crate_battles.value[index].is_group) {
            return "group";
        }
    }

    async function getCrateBattles() {
        let response = await axios.get("/api/crate-battle/list");

        if(!response.data.error) {
            crate_battles.value = response.data.data;
        }
    }

    function battleCreated(data) {
        crate_battles.value.push(data.battle);
    }

    function battleCompleted(data) {
        crate_battles.value = crate_battles.value.filter(battle => data.id !== battle.id);
    }
</script>
