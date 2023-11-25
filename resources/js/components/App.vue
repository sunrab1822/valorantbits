<template>
    <Navbar/>
    <main class="py-4">
        <div class="container">
            <RouterView/>
        </div>
    </main>
    <Login/>
    <Register />
</template>

<script setup>
    import { useUserStore } from '@stores/user';

    const props = defineProps(['user', 'isAuth']);

    const userStore = useUserStore();

    userStore.setUser(props.user ? JSON.parse(props.user) : null);
    userStore.setLoggedIn(props.isAuth);

</script>
