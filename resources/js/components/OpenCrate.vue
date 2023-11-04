<template>
    <div class="row">
        <div class="col">
            <button class="btn btn-success" @click="back" >&lsaquo;  Back</button>
        </div>
        <div class="col text-center">
            <h1 class="">{{ crateObj.name }}</h1>
        </div>
        <div class="col"></div>
    </div>

    <div class="spin-wrapper mb-3">
        <div class="spin-selector"></div>
        <div class="spin-wheel d-flex" style="transform: translate3d(7098px, 0px, 0px)">
            <div v-for="(skin, key) in spinItems" class="spin-element" :class="`element-`+key">
                <img :src="skin.image" alt="">
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <div class="bg-secondary rounded me-2">
            <button class="btn btn-secondary">1</button>
            <button class="btn btn-secondary">2</button>
            <button class="btn btn-secondary">3</button>
            <button class="btn btn-secondary">4</button>
        </div>
        <div>
            <button class="btn btn-success" @click="openCrate()" :disabled="isSpinning">Open</button>
        </div>
    </div>
    <div>
        <h3>Crate items</h3>
        <div class="row">
            <CrateItem v-for="skin in crateObj.contents" :name="skin.name" :image="skin.image" :chance="skin.chance"></CrateItem>
        </div>
    </div>


</template>

<script setup>
    import { ref } from 'vue';
    import axios from 'axios';

    const prop = defineProps(['crate'])
    let crateObj = JSON.parse(prop.crate)
    console.log(crateObj);

    let isSpinning = ref(false);
    let spinItems = ref([]);

    function initCrate(winning_item = null) {
        let chances = {};
        let chance_num = 0;
        Object.values(crateObj.contents).forEach((value) => {
            chance_num += Math.round(10000*(value.chance/100));
            chances[chance_num] = value["skin_id"];
        });

        spinItems.value = [];
        for(let i = 0; i < 100; i++) {
            let randomNum = getRandomInt(0, 10001);
            for(const [chanceLimit, skin_id] of Object.entries(chances)) {
                if(randomNum <= chanceLimit) {
                    spinItems.value.push(crateObj.contents[Object.keys(crateObj.contents).find(key => crateObj.contents[key]["skin_id"] === skin_id)]);
                    break;
                }
            }
        }

        if(winning_item) {
            let $winningElement = $(".spin-wheel .spin-element:nth-child(81)");

            $winningElement.find("img").attr('src', winning_item.image);
        }
    }

    initCrate();

    async function openCrate() {
        isSpinning.value = true;
        let $wheel = $('.spin-wrapper .spin-wheel');
        $wheel.css({'transform': 'translate3d(7098px, 0px, 0px)'});
        let openedItem = await axios.post("/api/crate/open", {
            'clientSeed': '123456789',
            'id': crateObj.id
        });

        if(openedItem.data.data.drop == undefined || openedItem.data.data.drop == null) {
            isSpinning.value = false;
            return;
        }

        initCrate(openedItem.data.data.drop);

        setTimeout(doOpenCrate, 400);
    }

    function doOpenCrate() {
        let card_width = 150;
        let $wheel = $('.spin-wrapper .spin-wheel');
        let position = getRandomInt(0, 15);
        let card = card_width + 3 * 2;
        let landingPosition = (15 * card) + (position * card);
        let randomize = Math.floor(Math.random() * card_width) - (card_width/2);

        landingPosition = landingPosition + randomize;

        let object = {
            x: Math.floor(Math.random() * 50) / 100,
            y: Math.floor(Math.random() * 20) / 100
        };

        $wheel.css({
            'transition-timing-function':'cubic-bezier(0,'+ object.x +','+ object.y + ',1)',
            'transition-duration':'6s',
            'transform':'translate3d(-4758px, 0px, 0px)'
        });

        setTimeout(postOpenCrate, 6 * 1000);
    }

    function postOpenCrate() {
        isSpinning.value = false;
        let $wheel = $('.spin-wrapper .spin-wheel');
        $wheel.css({
            'transition-timing-function':'',
            'transition-duration':'',
        });
    }

    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min)) + min;
    }

    function back(){
        history.back()
    }
</script>
