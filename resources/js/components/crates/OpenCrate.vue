<template>
    <div class="row">
        <div class="col">
            <button class="btn btn-success" @click="back" >&lsaquo;&nbsp;Back</button>
        </div>
        <div class="col text-center">
            <h1 v-if="crate != null">{{ crate.name }}</h1>
        </div>
    <div class="col"></div>
    </div>
    <div class="spin-wrapper mb-3">
        <div class="spin-selector"></div>
        <div class="spin-wheel d-flex" style="transform: translate3d(7098px, 0px, 0px)">
            <div v-for="(skin, key) in spinItems" class="spin-element flex-column">
                <img :src="skin.image" alt="">
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center align-items-center">
        <div>
            <button class="btn btn-success" @click="openCrate()" :disabled="isSpinning" v-if="userStore.isLoggedIn">Open</button>
        </div>
        <div class="d-flex justify-content-center align-items-center ms-1" v-if="crate != null">
            <img src="/storage/radianite.png" class="currency-icon"/><span class="fs-5">{{ crate.price.toBalance(2) }}</span>
        </div>
    </div>
    <div>
        <h3>Crate items</h3>
        <div class="row row-cols-xl-5 row-cols-xs-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4" style="--bs-gutter-x: 0.5rem; --bs-gutter-y: 0.5rem;">
            <CrateItem v-if="crate != null" v-for="skin in crate.contents" :tier="skin.tier.devName" :name="skin.name" :image="skin.image" :chance="skin.chance" :price="skin.price"></CrateItem>
        </div>
    </div>
</template>

<script setup>
    import { useRoute } from 'vue-router'
    import { ref, nextTick } from 'vue';
    import { useUserStore } from '@stores/user';
    import axios from 'axios';
    import random from 'random-seedable';

    const route = useRoute();
    const userStore = useUserStore();
    let crate = null;

    let isSpinning = ref(false);
    let spinItems = ref([]);

    async function getCrate() {
        let request = await axios("/api/crate/" + route.params.id);

        console.log(request);

        if(!request.data.error) {
            crate = request.data.data;
            crate.contents.sort(function(a,b) {
                return a.tier.rank > b.tier.rank ? -1 : 1;
            });

            initCrate();
        }
    }

    getCrate();

    async function initCrate(winning_item = null) {
        let chances = {};
        let chance_num = 0;
        Object.values(crate.contents).forEach((value) => {
            chance_num += Math.round(10000*(value.chance/100));
            chances[chance_num] = value["skin_id"];
        });

        spinItems.value = [];
        for(let i = 0; i < 100; i++) {
            let randomNum = getRandomInt(0, 10001);
            for(const [chanceLimit, skin_id] of Object.entries(chances)) {
                if(randomNum <= chanceLimit) {
                    spinItems.value.push(crate.contents[Object.keys(crate.contents).find(key => crate.contents[key]["skin_id"] === skin_id)]);
                    break;
                }
            }
        }

        let $wheel = $('.spin-wrapper .spin-wheel');
        $wheel.css({'transform': 'translate3d(7098px, 0px, 0px)'});
        $wheel.find(".won_item_text_container").remove();
        $wheel.find(":nth-child(81)").removeClass("border-success");
        $(".spin-wrapper .spin-selector").removeClass("d-none");
        await nextTick();

        if(winning_item) {
            spinItems.value[80] = winning_item;
        }
    }

    async function openCrate() {
        isSpinning.value = true;
        let openedItem = await axios.post("/api/crate/open", {
            'clientSeed': '123456789',
            'id': crate.id
        });

        if(!openedItem.data.error) {
            $(".nav_balance").trigger("updateBalance", crate.price * -1);
        }


        if(openedItem.data.data.drop == undefined || openedItem.data.data.drop == null) {
            isSpinning.value = false;
            return;
        }

        await initCrate(openedItem.data.data.drop);

        setTimeout(doOpenCrate, 400, openedItem.data.data.drop);
    }

    function doOpenCrate(wonItem) {
        let card_width = 150;
        let $wheel = $('.spin-wrapper .spin-wheel');

        let randomize = Math.floor(Math.random() * card_width) - (card_width/2);
        let landingPosition = 4758 + randomize;

        let object = {
            x: Math.floor(Math.random() * 50) / 100,
            y: Math.floor(Math.random() * 20) / 100
        };

        $wheel.css({
            'transition-timing-function':'cubic-bezier(0,'+ object.x +','+ object.y + ',1)',
            'transition-duration':'6s',
            'transform':'translate3d(-'+ landingPosition +'px, 0px, 0px)'
        });

        setTimeout((wonItem) => {
            $('.spin-wrapper .spin-wheel').css({
                'transition-timing-function':'linear',
                'transition-duration':'0.2s',
                'transform':'translate3d(-4758px, 0px, 0px)'
            });
            setTimeout(postOpenCrate, 400, wonItem);
        }, 6100, wonItem);
    }

    function postOpenCrate(wonItem) {
        let $wheel = $('.spin-wrapper .spin-wheel');

        let $winItemTextContainer = $("<div>", {
            class: "won_item_text_container fs-7 text-center mt-4"
        });

        $winItemTextContainer.append($("<div>", {
            text: wonItem.name
        }));
        $winItemTextContainer.append($("<div>", {
            text: wonItem.price.toBalance(2)
        }).prepend($("<img>", {"src": "/storage/radianite.png", "class": "currency-icon"})));

        $(".spin-wrapper .spin-selector").addClass("d-none");
        $wheel.find(":nth-child(81)").append($winItemTextContainer);
        $wheel.find(":nth-child(81)").addClass("border-success");

        $wheel.css({
            'transition-timing-function':'',
            'transition-duration':'',
        });
        $(".nav_balance").trigger("updateBalance", wonItem.price);
        isSpinning.value = false;
    }

    function getRandomInt(min, max) {
        return Math.floor(random.float53() * (max - min)) + min;
    }

    function back(){
        history.back()
    }
</script>
