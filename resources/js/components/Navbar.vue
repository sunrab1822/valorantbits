<template>
        <nav class="navbar navbar-expand-md navbar-dark bg-nav-dark shadow-sm">
            <div class="container-fluid mx-3 fixed-navbar-height">
                <router-link class="navbar-brand brand-small bg-secondary p-25 rounded-2" :to="{ name: 'home' }">
                    <svg class="d-block" stroke="#fff" fill="#fff" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M208 448V320h96v128h97.6V256H464L256 64 48 256h62.4v192z"></path></svg>
                </router-link>
                <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <router-link class="navbar-brand brand-expanded" :to="{ name: 'home' }">
                        <!-- {{ config('app.name', 'Laravel') }} -->
                        VALORANTBETS
                    </router-link>
                    <router-link class="navbar-brand brand-medium bg-secondary p-25 rounded-2" :to="{ name: 'home' }">
                        <svg class="d-block" stroke="#fff" fill="#fff" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M208 448V320h96v128h97.6V256H464L256 64 48 256h62.4v192z"></path></svg>
                    </router-link>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item btn-gray rounded-2">
                            <router-link class="nav-link" :to="{ name: 'crates'}">Crates</router-link>
                        </li>
                    </ul>
                </div>

                <!-- @auth -->
                <div class="mx-auto order-0 nav-balance">
                    <div class="d-flex bg-secondary align-items-center rounded-2 balance-box mx-auto">
                        <img src="/storage/radianite.png" class="currency-icon">
                        <div class="balance-text nav_balance">{{ user.balance.toBalance(2) }}</div>
                    </div>
                </div>
                <!-- @endauth -->

                <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->

                        <li class="nav-item bg-primary rounded-2 me-1 px-1" v-if="!isAuth">
                            <router-link class="nav-link" :to="{ name: 'login'}">Login</router-link>
                        </li>
                        <li class="nav-item bg-secondary rounded-2 px-1" v-if="!isAuth">
                            <router-link class="nav-link" :to="{ name: 'register'}">Register</router-link>
                        </li>
                        <li class="nav-item dropdown user-profile-dropdown btn-gray rounded-2" data-bs-theme="dark" v-else>
                            <div id="navbarDropdown" class="nav-link d-flex" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <div class="pe-1" style="width: 9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <div>
                                        <span class="user-level me-1">21</span>
                                        <span class="no-wrap">{{ user.username }}</span>
                                    </div>
                                    <div class="progress" role="progressbar">
                                        <div class="progress-bar user-level-progress" style="width: 95%"></div>
                                    </div>
                                </div>
                                <div class="dropdown-toggle"></div>
                            </div>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <router-link class="dropdown-item" :to="{name:'profile'}">Profile</router-link>
                                <a class="dropdown-item" >Logout</a>

                            </div>

                            <!-- <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-item">
                                    Profile
                                </div>

                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div> -->
                        </li>
                    </ul>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
</template>

<script setup>

    const props = defineProps(['user', 'isAuth']);
    let user = JSON.parse(props.user);

</script>
