<template>
    <div class="modal modal-lg fade" id="crateBattleModal" tabindex="-1" data-bs-theme="dark">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Select Case</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <input id="crate-battle-search" type="text" class="form-control" name="search" v-model="search" placeholder="Search" @keyup="">
                            </div>
                        </div>
                        <div class="row row-cols-4 gx-3">
                            <div class="col p-1 crate-select-card" v-for="(crate, key) in filteredCrates" @click="addCrate(key)">
                                <div class="rounded bg-nav-dark d-flex flex-column align-items-center justify-content-center py-4 px-2">
                                    <img :src="crate.image" style="width: 4rem; height: 4rem;" alt="">
                                    <div>{{ crate.name }}</div>
                                    <div class="d-flex">
                                        <currency></currency>
                                        {{ crate.price.toBalance(2) }}
                                    </div>
                                </div>
                            </div>
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
    import { ref, computed } from 'vue';
    import { useUserStore } from '@stores/user';

    const emit = defineEmits(["addCrate"]);

    const userStore = useUserStore();

    let crates = ref([]);
    let search = ref("");
    let filteredCrates = computed(() => {
        if(search.value) {
            return crates.value.filter((crate) => {
                return search.value.toLowerCase().split(" ").every((v) => crate.name.toLowerCase().includes(v));
            });
        } else {
            return crates.value;
        }

    });

    getCrates();

    async function getCrates() {
        let response = await axios.get("/api/crate-list");

        if(!response.data.error) {
            crates.value = response.data.data;
        }
    }

    function addCrate(key) {
        emit("addCrate", filteredCrates.value[key]);
    }

</script>
