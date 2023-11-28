<template>
    <div class="modal fade" id="coinflipCreateModal" tabindex="-1" data-bs-theme="dark">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" v-if="isCreate">Create</div>
                <div class="modal-header" v-else>Join Game</div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="d-flex justify-content-center">
                            <input type="text" class="form-control w-50 text-center" name="bet_amount"
                                v-model="masik" required autofocus>
                            <span class="invalid-feedback" role="alert" v-if="validation.bet_amount">
                                <strong>{{ validation.bet_amount }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-secondary" @click="bet_amount+= 100">+1</button>
                            <button class="btn btn-secondary ms-1" @click="bet_amount+= 1000">+10</button>
                            <button class="btn btn-secondary ms-1" @click="bet_amount = 1">Min</button>
                            <button class="btn btn-secondary ms-1" @click="bet_amount+= 300000">Max</button>
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


const props = defineProps(["isCreate"])

const router = useRouter();
const route = useRoute();

let bet_amount = ref(1);
let validation = ref({
        bet_amount: null,
        bet_side: null,
    });
let bet_side = ref("heads");


let masik = computed(() => {
    if (bet_amount.value > 300000) bet_amount.value = 300000
    return bet_amount.value.toBalance(2)
});

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
