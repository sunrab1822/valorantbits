<template>
    <div class="row shadow background">
        <div class="overflow-hidden col-xl-5 col-lg-5 d-xl-flex d-lg-flex d-block align-items-center">
            <router-link class="navbar-brand no-active" :to="{ name: 'home' }">
                VALORANTBETS
            </router-link>
        </div>
        <div class="col-xl-2 col-lg-2 d-xl-flex d-lg-flex d-block align-items-center">
            <div class="d-flex bg-secondary rounded align-items-center rounded-2 balance-box mx-auto">
                <currency></currency>
                <div class="balance-text nav_balance">{{ userStore.user.balance.toBalance(2) }}</div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-5">
            <nav class="navbar navbar-expand-md navbar-dark">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item rounded-2">
                            <router-link class="nav-link" :to="{ name: 'crates'}">Crates</router-link>
                        </li>
                        <li class="nav-item rounded-2 ms-2">
                            <router-link class="nav-link" :to="{ name: 'crate_battles'}">Crate Battles</router-link>
                        </li>
                        <li class="nav-item rounded-2 ms-2">
                            <router-link class="nav-link" :to="{ name: 'coinflip'}">Coinflip</router-link>
                        </li>
                        <li class="nav-item rounded-2 ms-2" v-if="!userStore.isLoggedIn">
                            <button class="nav-link" type="button" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                        </li>
                        <li class="nav-item rounded-2 ms-2" v-if="!userStore.isLoggedIn">
                            <button class="nav-link" type="button" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
                        </li>
                        <li class="nav-item dropdown ms-2 user-profile-dropdown rounded-2 no-active" data-bs-theme="dark" v-else>
                            <div id="navbarDropdown" class="nav-link d-flex" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="me-1">
                                    <img :src="userStore.user.profile_image" class="profile-picture-2">
                                </div>
                                <div class="pe-1" style="width: 7rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <div>
                                        <span class="no-wrap">{{ userStore.user.username }}</span>
                                    </div>
                                    <div class="progress" role="progressbar">
                                        <div class="progress-bar user-level-progress" style="width: 95%"></div>
                                    </div>
                                </div>
                                <div class="dropdown-toggle"></div>
                            </div>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <router-link class="dropdown-item" :to="{ name: 'profile', params: userStore.user.id }">Profile</router-link>
                                <button class="dropdown-item" @click="logout">Logout</button>
                            </div>
                        </li>
                    </ul>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>
        </div>
    </div>
</template>

<script setup>
    import axios from 'axios';
    import { useUserStore } from '@stores/user';

    const userStore = useUserStore();

    async function logout() {
        let request = await axios.post("/api/logout");

        if(!request.data.error) {
            userStore.setUser(null);
            userStore.setLoggedIn(false);
        }
    }
</script>
