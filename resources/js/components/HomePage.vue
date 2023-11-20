<template>
    <div class="row row-cols-4 g-2 main-page-cards mx-auto">
        <div class="game-choice-card">
            <router-link class="styleless-link" :to="{ name: 'crates' }">
                <div class="m-2 game-choice-inner-card d-flex justify-content-center" @click="">
                    <img class="card-img" src="/storage/crate.png">
                </div>
                <h4 class="text-center">
                    Open Crates
                </h4>
            </router-link>
        </div>
        <div class="game-choice-card">
            <a class="styleless-link" href="/crate-battles">
                <div class="m-2 game-choice-inner-card d-flex justify-content-center" @click="">
                    <img class="card-img" src="/storage/crate.png">
                </div>
                <h4 class="text-center">
                    Crate Battles
                </h4>
            </a>
        </div>
        <div class="game-choice-card">
            <a class="styleless-link" href="/coinflip">
                <div class="m-2 game-choice-inner-card d-flex justify-content-center" @click="">
                    <img class="card-img" src="/storage/crate.png">
                </div>
                <h4 class="text-center">
                    Coinflip
                </h4>
            </a>
        </div>
        <div class="game-choice-card">
            <a class="styleless-link" href="/crates">
                <div class="m-2 game-choice-inner-card d-flex justify-content-center" @click="">
                    <img class="card-img" src="/storage/crate.png">
                </div>
                <h4 class="text-center">
                    Blackjack
                </h4>
            </a>
        </div>
    </div>
</template>

<script >

</script>

<style>

</style>
