<template>
    <div class="modal fade" id="coinflipCreateModal" tabindex="-1" data-bs-theme="dark">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" v-if="isCreate">Create</div>
                <div class="modal-header" v-else>Join Game</div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="d-flex justify-content-center">
                            <input type="number" class="form-control w-50 text-center" name="bet_amount"
                                v-model="bet_amount"  required autofocus>
                            <span class="invalid-feedback" role="alert" v-if="validation.bet_amount">
                                <strong>{{ validation.bet_amount }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-secondary" @click="setBetAmount(1)">1/2</button>
                            <button class="btn btn-secondary ms-1" @click="setBetAmount(2)">x2</button>
                            <button class="btn btn-secondary ms-1" @click="setBetAmount(3)">Min</button>
                            <button class="btn btn-secondary ms-1" @click="setBetAmount(4)">Max</button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="d-flex justify-content-center">
                            <input type="radio" class="btn-check" name="bet_side" v-model="bet_side" value="heads" id="heads" :checked="isCreate" :disabled="!isCreate">
                            <label class="btn btn-secondary" for="heads">Heads</label>

                            <input type="radio" class="btn-check" name="bet_side" v-model="bet_side" value="tails" id="tails" :disabled="!isCreate">
                            <label class="btn btn-secondary ms-1" for="tails">Tails</label>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary" v-if="isCreate" @click="create">
                                Create
                            </button>
                            <button type="submit" class="btn btn-primary" v-if="!isCreate" @click="join">
                                Join
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import * as bootstrap from 'bootstrap';
import { ref,computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '@stores/user';

const userStore = useUserStore();



const props = defineProps(["isCreate"])

const router = useRouter();
const route = useRoute();

let bet_amount = ref(0.01);
let validation = ref({
        bet_amount: null,
        bet_side: null,
    });
let bet_side = ref("heads");

function setBetAmount(type){
    console.log(Math.round((bet_amount.value + Number.EPSILON) * 100), userStore.user.balance)
    switch (type){
        case 1: bet_amount.value = bet_amount.value >= 0.01 ? (Math.round(((bet_amount.value / 2) + Number.EPSILON) * 100)).toBalance(2,true) :  0.01;
                break;
        case 2:  bet_amount.value = Math.round((bet_amount.value + Number.EPSILON) * 100) < userStore.user.balance ?  (Math.round(((bet_amount.value * 2) + Number.EPSILON) * 100)).toBalance(2,true) : userStore.user.balance.toBalance(2, true);
                break;
        case 3: bet_amount.value = 0.01;
                break;
        case 4: bet_amount.value = userStore.user.balance.toBalance(2,true);
                break;
    }
    console.log(bet_amount.value)
}

async function create() {
    for(const key of Object.keys(validation.value)) {
        validation.value[key] = null;
    }

    let request = await axios.post('/api/coinflip', {
        bet_amount: bet_amount.value,
        bet_side: bet_side.value
    })

    if(!request.data.error) {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('coinflipCreateModal')).hide();
        bet_amount.value = 1;
        bet_side.value = "heads";
        router.push({name:"coinflip_game", params:{id:request.data.data}})
    } else {
        if(request.data.error_type == "validation") {
            for(const key of Object.keys(request.data.data)) {
                validation.value[key] = request.data.data[key][0];
            }
        }
    }
}


async function join() {
    await axios.post("/api/coinflip/join", {
        game_id: route.params.id,
        bet_amount: bet_amount.value
    });
    bootstrap.Modal.getOrCreateInstance(document.getElementById('coinflipCreateModal')).hide();
}
</script>
