<template>
    <div class="" style="min-height: 100vh;">
        <div class="container-fluid">
            <Navbar />
        </div>
        <main class="px-2">
            <div class="container py-4">
                <RouterView/>
            </div>
        </main>
    </div>

    <Vault />
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
