<template>
    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-success" @click="back">Back</button>
        <button class="btn btn-primary" @click="createBattle">Create</button>
    </div>
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex">
            <select name="battleType" class="me-1">
                <option value="1">1v1</option>
                <option value="2">1v1v1</option>
                <option value="3">1v1v1v1</option>
                <option value="4">2v2</option>
            </select>
            <button class="btn btn-secondary me-1">Normal</button>
            <button class="btn btn-secondary me-1">Group</button>
            <button class="btn btn-secondary">Terminal</button>
        </div>
        <div>
            <button class="btn btn-secondary">Crazy</button>
        </div>
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
                        <div>{{ crate.price }}</div>
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

    let crates = ref([]);

    function back() {
        history.back();
    }

    function createBattle() {

    }

    function addCrate() {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('crateBattleModal')).show()
    }

    function addCrateCb(crate) {
        crates.value.push(crate);
    }
</script>
