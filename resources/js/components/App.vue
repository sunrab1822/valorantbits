<template>
    <Navbar :username="user.username" :balance="user.balance" :isAuth="isAuth" @is-logged-in="updateLoggedIn"/>
    <main class="py-4">
        <div class="container">
            <RouterView :is-auth="isAuth"/>
        </div>
    </main>
    <Login @is-logged-in="updateLoggedIn"/>
    <Register />
</template>

<script setup>
    import { ref } from 'vue';

    const props = defineProps(['user', 'isAuth']);

    let isAuth = ref(props.isAuth);
    let user = JSON.parse(props.user || "{}");

    function updateLoggedIn(isLoggedIn) {
        isAuth.value = isLoggedIn;
    }
</script>
