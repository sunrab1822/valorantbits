<template>
    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-secondary" @click="back">Back</button>
        <button class="btn btn-primary" @click="createBattle">Create</button>
    </div>
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex">
            <select name="battleType" v-model="battle_type" class="me-1 form-select battle-mode-select bg-light-gray">
                <option value="1">1v1</option>
                <option value="2">1v1v1</option>
                <option value="3">1v1v1v1</option>
                <option value="4">2v2</option>
            </select>
            <div class="me-1">
                <input type="radio" class="btn-check" name="battle_options" v-model="battle_options" id="normal" value="normal" autocomplete="off" checked>
                <label class="btn btn-gray" for="normal">Normal</label>
            </div>
            <div class="me-1">
                <input type="radio" class="btn-check" name="battle_options" v-model="battle_options" id="group" value="group" autocomplete="off">
                <label class="btn btn-gray" for="group">Group</label>
            </div>
            <div>
                <input type="radio" class="btn-check" name="battle_options" v-model="battle_options" id="terminal" value="terminal" autocomplete="off">
                <label class="btn btn-gray" for="terminal">Terminal</label>
            </div>
        </div>
        <!-- <div class="bg-secondary form-check form-switch rounded">
            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked">
            <label class="form-check-label" for="flexSwitchCheckChecked">Crazy</label>
        </div> -->
    </div>
    <div class="row gx-3">
        <div class="col-md-3">
            <div class="crate-card d-flex align-items-center justify-content-center bg-nav-dark rounded" @click="addCrate">
                <svg stroke="#ffffff" fill="#ffffff" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"></path></svg>
                <div>Add Crate</div>
            </div>
        </div>
        <div class="col-md-3 mb-3" v-for="crate in crates">
            <div class="crate-card d-flex bg-nav-dark rounded">
                <div class="d-flex flex-column align-items-center justify-content-center">
                    <img class="" :src="crate.image" alt="" style="max-width: 50%;">
                    <div>{{ crate.name }}</div>
                    <div class="d-flex">
                        <currency />
                        <div>{{ crate.price.toBalance(2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <CrateBattlePopup @add-crate="addCrateCb"></CrateBattlePopup>
</template>

<script setup>
    import { ref } from 'vue';
    import * as bootstrap from 'bootstrap';
    import { useRouter } from 'vue-router';

    const router = useRouter();
    let crates = ref([]);
    let battle_options = ref("normal");
    let battle_type = ref(1);

    function back() {
        history.back();
    }

    async function createBattle() {
        let response = await axios.post("/api/crate-battle", {
            crates: crates.value.map(obj => obj.id),
            type: battle_type.value,
            options: battle_options.value
        });

        if(!response.data.error) {
            router.push({name:"crate_battles_game", params: {id: response.data.data}});
        }
    }

    function addCrate() {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('crateBattleModal')).show()
    }

    function addCrateCb(crate) {
        crates.value.push(crate);
    }
</script>
