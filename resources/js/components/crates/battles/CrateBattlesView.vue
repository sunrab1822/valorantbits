<template>
    <div class="container-fluid">
        <div class="provably-fair-text"><u>Provably fair</u></div>
        <div class="row mb-3 bg-nav-dark">
            <div class="col d-flex justify-content-center">
                <div class="w-100" style="max-width: 28rem;">
                    <div class="d-flex crate-list-image" style="transform: translate3d(calc(50% - 24px), 0, 0);">
                        <img :src="crate.image" alt="" v-for="(crate, key) in crate_battle.crate_list" style="width: 48px; height: 48px;" :class="{'me-1': key+1 != crate_battle.crate_list.length, 'grayscale': key != currentCrateIndex}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row gx-4" :class="{'row-cols-4': numberOfPlayers == 4, 'row-cols-3': numberOfPlayers == 3, 'row-cols-2': numberOfPlayers == 2}">
            <div class="col bg-nav-dark" v-for="i in numberOfPlayers">
                <div class="d-flex p-3 border-bottom border-success" :class="{'justify-content-between': crate_battle.player_list[i-1], 'justify-content-end': crate_battle.player_list[i-1] == undefined}">
                    <div class="d-flex" v-if="crate_battle.player_list[i-1]">
                        <img :src="crate_battle.player_list[i-1].profile_image" class="profile-picture-3 me-3 border border-success">
                        <div v-if="crate_battle.player_list[i-1] != undefined">
                            <div class="fs-5">{{ crate_battle.player_list[i-1].username }}</div>
                            <div class="bg-primary px-2 rounded fs-7" v-if="crate_battle.player_list[i-1].is_bot">BOT</div>
                        </div>
                    </div>
                    <div v-else>
                        <button class="btn btn-success fs-5 px-3" v-if="!userStore.user" disabled>Login</button>
                        <button class="btn btn-success fs-5 px-3" style="height: 3rem;" @click="joinBattle(i-1)" v-else-if="userStore.user && userStore.user.id != crate_battle.created_by">Join</button>
                        <button class="btn btn-success fs-5 px-3" style="height: 3rem;" @click="joinBattle(-1)" v-else>Call Bots</button>
                    </div>
                    <div class="battle-player-earnings" v-if="crate_battle.player_list[i-1]">
                        <img src="/storage/radianite.png" class="currency-icon">
                        <div class="fs-5">{{ itemPrices[i-1].toBalance(2) }}</div>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="spin-wrapper-horizontal">
                        <div class="spin-selector"></div>
                        <div class="spin-wheel d-flex" style="transform: translate3d(0px, -210px, 0px)">
                            <div v-for="(skin, key) in spinItems[currentCrateIndex][i-1]" class="spin-element flex-column">
                                <img :src="skin.image">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="won-items mb-3">
                    <div class="won-item-card" v-for="(round, index) in wonItems" :class="{'mb-2': index + 1 < wonItems.length}">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 8rem; height: 4rem;">
                            <img :src="round[i-1].image" style="max-width: 100%;max-height: 2rem;">
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <div class="fs-5">{{ round[i-1].name }}</div>
                            <div class="d-flex">
                                <img src="/storage/radianite.png" class="currency-icon">
                                <div class="fs-6">{{ round[i-1].price.toBalance(2) }}</div>
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
    import { ref, nextTick } from 'vue';
    import { useRoute } from 'vue-router';
    import { XORShift } from 'random-seedable';
    import { useUserStore } from '@stores/user';

    let crate_battle = ref({});
    let spinItems = ref([]);
    let currentCrateIndex = ref(0);
    let numberOfPlayers = ref(0);
    let wonItems = ref([]);
    let itemPrices = ref([0, 0, 0, 0]);

    let random = new XORShift(crate_battle.seed);
    const route = useRoute();
    const userStore = useUserStore();

    getCrateBattle();

    window.Echo.channel("CrateBattle." + route.params.id)
        .listen(".battle-join", battleJoin)
        .listen(".battle-roll", battleRoll);

    function battleJoin(data) {
        crate_battle.value.player_list[data.spot] = data.joined_user;
    }

    function battleRoll(data) {
        if(data.crate_number != 0) {
            currentCrateIndex.value = data.crate_number;
            resetSpin();
        }
        setTimeout(doBattle, 400, data.result);
    }

    async function resetSpin() {
        let $wheel = $('.spin-wrapper-horizontal .spin-wheel');
        $wheel.each(function(){
            let $this = $(this);
            $this.css({'transform': 'translate3d(0px, -210px, 0px)'});
            $this.find(":nth-child(80)").removeClass("border-success");
        });
        let crate_image_position = 24 + currentCrateIndex.value*52;
        $(".crate-list-image").css({'transform': 'translate3d(calc(50% - '+crate_image_position+'px), 0, 0)'});
        await nextTick();
    }

    async function doBattle(result) {
        let $wheel = $('.spin-wrapper-horizontal .spin-wheel');
        let card_height = 150;
        let randomize = Math.floor(Math.random() * card_height) - (card_height/2);
        let landingPosition = 12220 + randomize;

        let object = {
            x: Math.floor(Math.random() * 50) / 100,
            y: Math.floor(Math.random() * 20) / 100
        };

        if(result) {
            result.forEach((item, key) => {
                spinItems.value[currentCrateIndex.value][key][79] = item;
            });
        }

        $wheel.each(function(){
            $(this).css({
                'transition-timing-function':'cubic-bezier(0,'+ object.x +','+ object.y + ',1)',
                'transition-duration':'6s',
                'transform':'translate3d(0px, -'+ landingPosition +'px, 0px)'
            });
        });

        setTimeout((result) => {
            $wheel.each(function(){
                $(this).css({
                    'transition-timing-function':'linear',
                    'transition-duration':'0.2s',
                    'transform':'translate3d(0px, -12220px, 0px)'
                });
            });
            setTimeout(postBattle, 400, result);
        }, 6100, result);
    }

    function postBattle(result) {
        wonItems.value[currentCrateIndex.value] = result;
        result.forEach((item, key) => {
            itemPrices.value[key] += item.price;
        });
        let $wheel = $('.spin-wrapper-horizontal .spin-wheel');

        /*let $winItemTextContainer = $("<div>", {
            class: "won_item_text_container fs-7 text-center mt-4"
        });

        $winItemTextContainer.append($("<div>", {
            text: wonItem.name
        }));
        $winItemTextContainer.append($("<div>", {
            text: wonItem.price.toBalance(2)
        }).prepend($("<img>", {"src": "/storage/radianite.png", "class": "currency-icon"})));*/

        //$(".spin-wrapper-horizontal .spin-selector").addClass("d-none");
        //$wheel.find(":nth-child(80)").append($winItemTextContainer);

        $wheel.each(function() {
            let $this = $(this);
            $this.find(":nth-child(80)").addClass("border-success");
            $this.css({
                'transition-timing-function':'',
                'transition-duration':'',
            });
        });
    }

    async function initCrate() {
        switch(crate_battle.value.battle_type) {
            case 1:
                numberOfPlayers = 2;
                break;
            case 2:
                numberOfPlayers = 3;
                break;
            case 3:
            case 4:
                numberOfPlayers = 4;
                break;
        }

        for(let x = 0; x < crate_battle.value.crate_list.length; x++) {
            let crate = crate_battle.value.crate_list[x];
            let chances = {};
            let chance_num = 0;
            Object.values(crate.contents).forEach((value) => {
                chance_num += Math.round(10000*(value.chance/100));
                chances[chance_num] = value["skin_id"];
            });

            spinItems.value[x] = [];
            for(let y = 0; y < numberOfPlayers; y++) {
                spinItems.value[x][y] = [];
                for(let i = 0; i < 100; i++) {
                    let randomNum = getRandomInt(0, 10000);
                    for(const [chanceLimit, skin_id] of Object.entries(chances)) {
                        if(randomNum <= chanceLimit) {
                            spinItems.value[x][y].push(crate.contents[Object.keys(crate.contents).find(key => crate.contents[key]["skin_id"] === skin_id)]);
                            break;
                        }
                    }
                }
            }

        }

        let $wheel = $('.spin-wrapper-horizontal .spin-wheel');
        $wheel.css({'transform': 'translate3d(0px, -210px, 0px)'});
        $wheel.find(".won_item_text_container").remove();
        $wheel.find(":nth-child(81)").removeClass("border-success");
        $(".spin-wrapper .spin-selector").removeClass("d-none");
        await nextTick();
    }

    async function getCrateBattle() {
        let response = await axios.get("/api/crate-battle/" + route.params.id);

        if(!response.data.error) {
            crate_battle.value = response.data.data;
            random = new XORShift(crate_battle.seed);
            initCrate();
        }
    }

    async function joinBattle(spot) {
        await axios.post("/api/crate-battle/join", {
            spot: spot,
            battleId: route.params.id
        });
    }

    function getRandomInt(min, max) {
        console.log(random.seed);
        return Math.floor(random.float53() * (max - min)) + min;
    }
</script>
