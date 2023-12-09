<template>
    <div class="bg-dark rounded-3 p-3" style="height: fit-content;">
        <div v-if="userStore.user">
            <router-link class="text-decoration-none text-white" :to="{name: 'profile'}">
                <div class="d-flex align-items-center">
                    <img class="profile-picture-3 rounded-circle border border-2" :src="userStore.user.profile_image" alt="">
                    <div class="flex-grow-1 fs-5 ps-3">
                        <div class="">
                            {{ userStore.user.username }}
                        </div>
                    </div>
                </div>
            </router-link>
            <div class="mt-3">
                <div class="progress bg-nav-dark" style="height: 0.5rem;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" :style="{ 'width': progressbar_width + '%' }"></div>
                </div>
            </div>
            <div class="d-flex border border-2 mt-3" style="border-radius: 0.7rem; border-color: gray !important;">
                <div class="rounded-start-3 fs-5 flex-grow-1 p-2">
                    <currency />
                    {{ userStore.user.balance.toBalance(2) }}
                </div>
                <div class="rounded-end-3 fs-5 bg-secondary py-2 px-3">
                    Earn
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-info w-100" @click="logout">Logout</button>
            </div>
        </div>
        <div class="d-flex" v-else>
            <button class="btn btn-secondary flex-fill rounded-3 text-center me-1" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
            <button class="btn btn-secondary flex-fill rounded-3 text-center ms-1" type="button" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
        </div>
    </div>
</template>

<script setup>
    import { useUserStore } from '@stores/user';
    import { ref } from 'vue';

    const userStore = useUserStore();

    let user = ref();
    let required_xp = ref(1000);
    let progressbar_width = ref(0);
    let current_level = ref(0);

    (async function() {
        user.value = userStore.user;
        current_level.value = (user.value.experience - 1000 < 0) ? 0 : Math.ceil((user.value.experience - 1000) / 250);
        required_xp.value = 1000 + ((current_level.value) * 250);
        progressbar_width.value = (user.value.experience / required_xp.value) * 100;

        console.log((user.value.experience / required_xp.value) * 100, required_xp.value);
    })();

    async function logout() {
        let request = await axios.post("/api/logout");

        if(!request.data.error) {
            userStore.setUser(null);
            userStore.setLoggedIn(false);
        }
    }
</script>
