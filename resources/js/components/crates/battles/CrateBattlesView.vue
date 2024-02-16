<template>
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-md-2 d-flex align-items-center">
                <button class="btn btn-secondary me-3 d-flex align-items-center h-75" @click="back" style="height: fit-content;">Back</button>
                <div class="fs-6-1" v-if="crate_battle.crate_list"><currency />{{ crate_battle.price.toBalance(2) }}</div>
            </div>
            <div class="col-md-8 text-center" v-if="showCrates">
                <div class="fs-4" v-if="crate_battle.crate_list">{{ crate_battle.crate_list[currentCrateIndex].name }}</div>
                <div class="fs-5" v-if="crate_battle.crate_list"><currency />{{ crate_battle.crate_list[currentCrateIndex].price.toBalance(2) }}</div>
            </div>
            <div class="col-md-8 text-center d-flex align-items-center justify-content-center" v-else>
                <button class="btn btn-primary d-flex align-items-center h-75" @click="back">Recreate</button>
            </div>
            <div class="col-md-2 text-end ls-1">
                <div class="fs-6-1">{{ getBattlePlayers() }}</div>
                <div class="fs-6-1 text-uppercase fw-bold">{{ getBattleType() }}</div>
            </div>
        </div>
        <div class="row mb-3 gx-0 bg-nav-dark rounded" v-if="showCrates">
            <div class="col d-flex justify-content-center">
                <div class="w-100 crate-list-image-container">
                    <div class="d-flex crate-list-image" style="transform: translate3d(calc(50% - 24px), 0, 0);">
                        <img :src="crate.image" alt="" v-for="(crate, key) in crate_battle.crate_list" style="width: 48px; height: 48px;" :class="{'me-1': key+1 != crate_battle.crate_list.length, 'grayscale': key != currentCrateIndex}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row gx-2" :class="{'row-cols-4': numberOfPlayers == 4, 'row-cols-3': numberOfPlayers == 3, 'row-cols-2': numberOfPlayers == 2}">
            <div class="col" v-for="i in numberOfPlayers">
                <div class="bg-nav-dark rounded">
                    <div class="d-flex p-3" :class="{'justify-content-between': crate_battle.player_list[i-1], 'justify-content-end': crate_battle.player_list[i-1] == undefined}">
                        <div class="d-flex" v-if="crate_battle.player_list[i-1]">
                            <img :src="crate_battle.player_list[i-1].profile_image" class="profile-picture-3 me-3 border border-primary">
                            <div v-if="crate_battle.player_list[i-1] != undefined">
                                <div class="fs-5">{{ crate_battle.player_list[i-1].username }}</div>
                                <div class="bg-primary px-2 rounded fs-7 bot-icon" v-if="crate_battle.player_list[i-1].is_bot">BOT</div>
                            </div>
                        </div>
                        <div v-else>
                            <button class="btn btn-primary fs-5 px-3" v-if="!userStore.user" style="height: 3rem;" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                            <button class="btn btn-primary fs-5 px-3" style="height: 3rem;" @click="joinBattle(i-1)" v-else-if="userStore.user && userStore.user.id != crate_battle.created_by" :disabled="joinButtonDisabled">Join</button>
                            <button class="btn btn-primary fs-5 px-3" style="height: 3rem;" @click="callBots()" v-else :disabled="callButtonDisabled">Call Bots</button>
                        </div>
                        <div class="battle-player-earnings" v-if="crate_battle.player_list[i-1]">
                            <currency />
                            <div class="fs-5">{{ itemPrices[i-1].toBalance(2) }}</div>
                        </div>
                    </div>
                    <div class="player_spin_body position-relative container-fluid" :class="{'bg-winner': winnerPlayers[i-1]}">
                        <div orientation="vertical" class="vertical-shadow"></div>
                        <div class="row py-2">
                            <div class="spin-wrapper-horizontal">
                                <div class="my-auto fs-4" :class="{'d-none': winnerPlayers.every(el => el == false)}"><currency />{{ winnerPlayers[i-1] ? wonAmount.toBalance(2) : "0.00" }}</div>
                                <div class="spin-selector" :class="{'d-none': winnerPlayers.some(el => el == true)}"></div>
                                <div class="spin-wheel d-flex" style="transform: translate3d(0px, -210px, 0px)" :class="{'d-none': winnerPlayers.some(el => el == true)}">
                                    <div v-for="(skin, key) in spinItems[currentCrateIndex][i-1]" class="spin-element flex-column">
                                        <img :src="skin.image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="won-items p-2">
                        <div class="won-item-card align-items-center" :class="{'mb-2': index != wonItems.length - 1}" v-for="(round, index) in wonItems.slice().reverse()">
                            <div class="d-flex align-items-center justify-content-center me-2" style="width: 8rem; height: 4rem;">
                                <img :src="round[i-1].image" style="max-width: 100%;max-height: 2rem;">
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <div class="fs-6-1">{{ round[i-1].name }}</div>
                                <div class="d-flex">
                                    <currency />
                                    <div class="fs-6">{{ round[i-1].price.toBalance(2) }}</div>
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
    import { ref, nextTick, onUnmounted } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { XORShift } from 'random-seedable';
    import { useUserStore } from '@stores/user';

    let crate_battle = ref({});
    let spinItems = ref([]);
    let currentCrateIndex = ref(0);
    let numberOfPlayers = ref(0);
    let wonItems = ref([]);
    let itemPrices = ref([0, 0, 0, 0]);
    let terminalItemPrices = ref([0, 0, 0, 0]);
    let callButtonDisabled = ref(false);
    let joinButtonDisabled = ref(false);
    let winnerPlayers = ref([false, false, false, false]);
    let wonAmount = ref(0);
    let timeoutID = ref(null);
    let showCrates = ref(true);

    let random = null;
    const route = useRoute();
    const router = useRouter();
    const userStore = useUserStore();

    getCrateBattle();

    onUnmounted(() => {
        if(timeoutID.value) {
            clearTimeout(timeoutID.value);
        }
    });

    window.Echo.channel("CrateBattle." + route.params.id)
        .listen(".battle-join", battleJoin)
        .listen(".call-bots", botsJoin)
        .listen(".battle-roll", battleRoll);

    function getBattlePlayers() {
        switch(crate_battle.value.battle_type) {
            case 1:
                return "1v1";
            case 2:
                return "1v1v1";
            case 3:
                return "1v1v1v1";
            case 4:
                return "2v2";
        }
    }

    function getBattleType() {
        if(crate_battle.value.is_normal) {
            if(crate_battle.value.is_crazy) {
                return "crazy";
            }
            return "normal";
        }
        if(crate_battle.value.is_terminal) {
            return (crate_battle.value.is_crazy ? "crazy " : "") + "terminal";
        }
        if(crate_battle.value.is_group) {
            return "group";
        }
    }

    function battleJoin(data) {
        crate_battle.value.player_list[data.spot] = data.joined_user;
    }

    function botsJoin(data) {
        Object.keys(data.bots).forEach(key => {
            crate_battle.value.player_list[key] = data.bots[key];
        });
    }

    function battleRoll(data) {
        if(data.crate_number != 0) {
            currentCrateIndex.value = data.crate_number;
            resetSpin();
        }
        timeoutID.value = setTimeout(doBattle, 100, data.result);
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

        timeoutID.value = setTimeout((result) => {
            $wheel.each(function(){
                $(this).css({
                    'transition-timing-function':'linear',
                    'transition-duration':'0.2s',
                    'transform':'translate3d(0px, -12220px, 0px)'
                });
            });
            timeoutID.value = setTimeout(postBattle, 100, result);
        }, 6100, result);
    }

    function postBattle(result) {
        wonItems.value[currentCrateIndex.value] = result;
        result.forEach((item, key) => {
            itemPrices.value[key] += item.price;
            if(currentCrateIndex.value == crate_battle.value.crate_list.length - 1) {
                terminalItemPrices.value[key] = item.price;
            }
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

        if(currentCrateIndex.value == crate_battle.value.crate_list.length - 1) {
            calculateWinners(true);
        }
    }

    function calculateWinners(showBalance = false) {
        if(crate_battle.value.is_group) {
            wonAmount.value = itemPrices.value.reduce((partialSum, a) => partialSum + a, 0) / numberOfPlayers;
            winnerPlayers.value = [true, true, true, true];
        } else {
            if(crate_battle.value.battle_type == 4) {
                let team_one = crate_battle.value.is_terminal ? terminalItemPrices.value[0] + terminalItemPrices.value[1] : itemPrices.value[0] + itemPrices.value[1];
                let team_two = crate_battle.value.is_terminal ? terminalItemPrices.value[2] + terminalItemPrices.value[3] : itemPrices.value[2] + itemPrices.value[3];
                wonAmount.value = itemPrices.value.reduce((partialSum, a) => partialSum + a, 0) / 2;

                if(team_one == team_two) {
                    let side_win = Math.floor(crate_battle.value.tie_float * 2);
                    let groupIndex = side_win * 2;
                    winnerPlayers.value[groupIndex] = true;
                    winnerPlayers.value[groupIndex + 1] = true;
                } else if(team_one > team_two) {
                    if(crate_battle.value.is_crazy) {
                        winnerPlayers.value = [false, false, true, true];
                    } else {
                        winnerPlayers.value = [true, true, false, false];
                    }
                } else {
                    if(crate_battle.value.is_crazy) {
                        winnerPlayers.value = [true, true, false, false];
                    } else {
                        winnerPlayers.value = [false, false, true, true];
                    }
                }
            } else {
                let playersWon = null;
                let amount = null;
                if(crate_battle.value.is_terminal) {
                    amount = crate_battle.value.is_crazy ? Math.min(...terminalItemPrices.value) : Math.max(...terminalItemPrices.value);
                    wonAmount.value = itemPrices.value.reduce((partialSum, a) => partialSum + a, 0);
                    playersWon = terminalItemPrices.value.map((amount, index) => ({ amount, index })).filter(({amount}) => amount == amount);
                } else {
                    amount = crate_battle.value.is_crazy ? Math.min(...itemPrices.value) : Math.max(...itemPrices.value);
                    wonAmount.value = itemPrices.value.reduce((partialSum, a) => partialSum + a, 0);
                    playersWon = itemPrices.value.map((amount, index) => ({ amount, index })).filter(({amount}) => amount == amount);
                }

                if(playersWon.length > 1) {
                    let player_indexes = Object.keys(playersWon);
                    let player_index = Math.floor(crate_battle.value.tie_float * crate_battle.value.player_list.length);
                    winnerPlayers.value[player_indexes[player_index]] = true;
                } else {
                    let playerWonIndex = crate_battle.value.is_terminal ? terminalItemPrices.value.indexOf(amount) : itemPrices.value.indexOf(amount);
                    winnerPlayers.value[playerWonIndex] = true;
                }
            }
        }

        if(showBalance && userStore.isLoggedIn && winnerPlayers.value[crate_battle.value.player_list.findIndex(player => player.id === userStore.user.id)]) {
            $(".nav_balance").trigger("updateBalance", wonAmount.value);
        }
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

        random = new XORShift(crate_battle.value.id);

        for(let x = 0; x < crate_battle.value.crate_list.length; x++) {
            let crate = crate_battle.value.crate_list[x];
            let chances = {};
            let chance_num = 0;
            Object.values(crate.contents).forEach((value) => {
                chance_num += Math.round(100000*(value.chance/100));
                chances[chance_num] = value["skin_id"];
            });

            spinItems.value[x] = [];

            for(let y = 0; y < numberOfPlayers; y++) {
                spinItems.value[x][y] = [];
                for(let i = 0; i < 100; i++) {
                    let randomNum = getRandomInt(0, 100000);
                    for(const [chanceLimit, skin_id] of Object.entries(chances)) {
                        if(randomNum <= chanceLimit) {
                            //console.log(randomNum, chanceLimit);
                            spinItems.value[x][y].push(crate.contents[Object.keys(crate.contents).find(key => crate.contents[key]["skin_id"] === skin_id)]);
                            break;
                        }
                    }
                }
            }
        }

        console.log(spinItems.value);

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
            if(crate_battle.value.player_list.some(player => userStore.user && player.id == userStore.user.id)) {
                joinButtonDisabled.value = true;
            }
            if(crate_battle.value.result != null) {
                wonItems.value = crate_battle.value.wonItems;
                itemPrices.value = crate_battle.value.totalEarnings;
                terminalItemPrices.value = crate_battle.value.totalEarningsTerminal;
            }
            initCrate();
            if(crate_battle.value.game_state == 2) {
                showCrates.value = false;
                calculateWinners();
            }
        }
    }

    async function joinBattle(spot) {
        if(!joinButtonDisabled.value) {
            joinButtonDisabled.value = true;
            await axios.post("/api/crate-battle/join", {
                spot: spot,
                battleId: route.params.id
            });
        }

    }

    async function callBots() {
        if(!callButtonDisabled.value) {
            callButtonDisabled.value = true;
            await axios.post("/api/crate-battle/call-bots", {
                battleId: route.params.id
            });
        }
    }

    function getRandomInt(min, max) {
        return Math.floor(random.float53() * (max - min)) + min;
    }

    function back() {
        router.push({name:"crate_battles"});
    }
</script>
