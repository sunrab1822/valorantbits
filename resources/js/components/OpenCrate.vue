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
        <div class="spin-wheel d-flex" style="transform: translate3d(78px, 0px, 0px)">
            <div v-for="skin in spinItems" class="spin-element">
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

    const prop = defineProps(['crate'])
    let crateObj = JSON.parse(prop.crate)
    console.log(crateObj);

    let isSpinning = ref(false);
    let spinItems = [];

    function initCrate() {
        for(let i = 0; i < 200; i++) {
            let randomNum = getRandomInt(0, crateObj.contents.length);
            spinItems.push(crateObj.contents[randomNum]);
        }
    }

    initCrate();

    function resetCratePosition() {
        let $wheel = $('.spin-wrapper .spin-wheel');
        $wheel.css({'transform': 'translate3d(78px, 0px, 0px)'});
        setTimeout(doOpenCrate, 400);
    }

    function openCrate() {
        isSpinning.value = true;
        resetCratePosition()
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
            'transform':'translate3d(-'+landingPosition+'px, 0px, 0px)'
        });

        setTimeout(function(){
            isSpinning.value = false;
            $wheel.css({
                'transition-timing-function':'',
                'transition-duration':'',
            });

            //let resetTo = -(position * card + randomize);
            //$wheel.css('transform', 'translate3d('+resetTo+'px, 0px, 0px)');
         }, 6 * 1000);
    }

    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min)) + min;
    }

    function back(){
        history.back()
    }
</script>
