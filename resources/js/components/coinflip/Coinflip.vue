<template>
    <div class="bg-dark mx-auto rounded p-2" v-if="coinflip">
        <div class="game-state d-flex justify-content-evenly">
            <div class="bg-warning-dark rounded px-2 py-1" v-if="coinflip.game_state == 0">Waiting...</div>
            <div class="bg-secondary rounded px-2 py-1" v-if="coinflip.game_state == 1">Ongoing</div>
            <div class="bg-danger rounded px-2 py-1" v-if="coinflip.game_state == 2">Over</div>
        </div>
        <div class="row py-2">
            <div class="col-md-4 d-flex justify-content-center align-items-center">
                <div class="user_side_heads">
                    <div class="d-flex align-items-center">
                        <img class="flip-profile-picture mb-2" v-if="created_by" :src="created_by.profile_image" alt="">
                            <img class="" src="/storage/crate_images/crate_red.png" style="width: 3rem; height: 3rem; margin-left: -1.5rem;">
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <div>{{ created_by.username }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex justify-content-center align-items-center">
                <div id="coin">
                    <div class="side-a">
                        <img class="w-100" src="/storage/crate_images/crate_red.png" alt="">
                    </div>
                    <div class="side-b">
                        <img class="w-100" src="/storage/crate_images/crate_red.png" alt="">
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex justify-content-center align-items-center">
                <div class="user_side_tails">
                    <div v-if="coinflip.game_state == 0">
                        <button class="btn btn-primary" v-if="!userStore.user" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                        <button class="btn btn-primary" v-else-if="userStore.user && created_by && created_by.id == userStore.user.id" @click="callBots">Call Bots</button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#coinflipCreateModal" v-else>Join</button>
                    </div>
                    <div v-else>
                        <img class="flip-profile-picture mb-2" :src="opponent.profile_image" alt="">
                        <div class="d-flex justify-content-center align-items-center">
                            <span>{{ opponent.username }}</span>
                            <span class="bg-primary px-2 rounded fs-7 ms-2" v-if="opponent.is_bot">BOT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <coinflip-create :isCreate="false"/>

</template>

<script setup>
    import axios from 'axios';
    import { useRoute } from 'vue-router';
    import { ref } from 'vue';
    import { useUserStore } from '@stores/user';

    const userStore = useUserStore();
    const route = useRoute();
    window.Echo.channel('Coinflip.' + route.params.id).listen(".coinflip-join", playerJoined);

    let coinflip = ref();
    let created_by = ref();
    let opponent = ref();
    let sideWon = ref(null);

    getCoinflip();

    async function getCoinflip(){
        let request = await axios.get('/api/coinflip/' + route.params.id);
        coinflip.value = request.data.data;
        created_by.value = coinflip.value["user_" + coinflip.value["created_by"]];
        opponent.value = coinflip.value["user_" + (coinflip.value["created_by"] == "heads" ? "tails" : "heads")];
        if(coinflip.value.game_state == 2) {
            sideWon.value = coinflip.value.chance_float < 0.5 ? "heads" : "tails";
        }
    }

    function playerJoined(data) {
        coinflip.value.game_state = 1;
        coinflip.value["user_" + data.side] = data.opponent;
        opponent.value = data.opponent;

        flipCoin();
    }

    async function callBots() {
        await axios.post("/api/coinflip/call-bots", {
            game_id: route.params.id
        });
    }

    function flipCoin() {
        var flipResult = Math.random();
        $('#coin').removeClass();
        setTimeout(function () {
            if (flipResult < 0.5) {
                $('#coin').addClass('heads');
            } else {
                $('#coin').addClass('tails');
            }

            setTimeout(postCoinFlip, 3500, flipResult);
        }, 500);
    }

    function postCoinFlip(result) {
        coinflip.value.game_state = 2;
        if(result < 5) {
            sideWon.value = "heads";
        } else {
            sideWon.value = "tails";
        }
    }
</script>

<style scoped>
.flip-profile-picture {
    height: 6.5rem;
    width: 6.5rem;
    border: 0.125rem solid white;
    border-radius: 50%;
}

#coin {
    position: relative;
    width: 125px;
    height: 125px;
    cursor: pointer;
}

#coin div {
    width: 100%;
    height: 100%;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    border-radius: 50%;
    -webkit-box-shadow: inset 0 0 45px rgba(255, 255, 255, .3), 0 12px 20px -10px rgba(0, 0, 0, .4);
    -moz-box-shadow: inset 0 0 45px rgba(255, 255, 255, .3), 0 12px 20px -10px rgba(0, 0, 0, .4);
    box-shadow: inset 0 0 45px rgba(255, 255, 255, .3), 0 12px 20px -10px rgba(0, 0, 0, .4);
    position: absolute;
    -webkit-backface-visibility: hidden;
    border: 0.125rem solid white;
}

.side-a {
    background-color: #bb000050;
    padding: 1.5rem;
}

.side-b {
    background-color: #3e3e3e;
    padding: 1.5rem;
}

#coin {
    transition: -webkit-transform 1s ease-in;
    -webkit-transform-style: preserve-3d;
}

.side-a {
    z-index: 100;
}

.side-b {
    -webkit-transform: rotateY(-180deg);

}

.user_side_heads, .user_side_tails {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 200px;
}

#coin.heads {
    -webkit-animation: flipHeads 3s ease-out forwards;
    -moz-animation: flipHeads 3s ease-out forwards;
    -o-animation: flipHeads 3s ease-out forwards;
    animation: flipHeads 3s ease-out forwards;
}

#coin.tails {
    -webkit-animation: flipTails 3s ease-out forwards;
    -moz-animation: flipTails 3s ease-out forwards;
    -o-animation: flipTails 3s ease-out forwards;
    animation: flipTails 3s ease-out forwards;
}

@-webkit-keyframes flipHeads {
    from {
        -webkit-transform: rotateY(0);
        -moz-transform: rotateY(0);
        transform: rotateY(0);
    }

    to {
        -webkit-transform: rotateY(1800deg);
        -moz-transform: rotateY(1800deg);
        transform: rotateY(1800deg);
    }
}

@-webkit-keyframes flipTails {
    from {
        -webkit-transform: rotateY(0);
        -moz-transform: rotateY(0);
        transform: rotateY(0);
    }

    to {
        -webkit-transform: rotateY(1980deg);
        -moz-transform: rotateY(1980deg);
        transform: rotateY(1980deg);
    }
}</style>
