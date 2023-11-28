<template>
    <div class="modal fade" id="coinflipCreateModal" tabindex="-1" data-bs-theme="dark">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">Create</div>

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
                            <button class="btn btn-secondary" @click="price+= 100">+1</button>
                            <button class="btn btn-secondary ms-1" @click="price+= 1000">+10</button>
                            <button class="btn btn-secondary ms-1" @click="price = 1">Min</button>
                            <button class="btn btn-secondary ms-1" @click="price+= 300000">Max</button>
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
                                Create
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
import { useRouter } from 'vue-router'


const props = defineProps(["isCreate"])

const router = useRouter();

let price = ref(1);
let validation = ref({
        bet_amount: null,
        bet_side: null,
    });
let bet_side = ref("heads");


let masik = computed(() => {
    if (price.value > 300000) price.value = 300000
    return price.value.toBalance(2)
});

async function create() {
    for(const key of Object.keys(validation.value)) {
        validation.value[key] = null;
    }

    let request = await axios.post('/api/coinflip', {
        bet_amount: price.value,
        bet_side: bet_side.value
    })

    if(!request.data.error) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('coinflipCreateModal')).hide();
            price.value = 1;
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


function join( ){

}
</script>
