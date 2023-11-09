<template>
    <div class="row">
        <div class="col col-12">
            <div class="d-flex justify-content-center">
                <img class="profp border border-success rounded-circle" src="{{ profileImage }}" alt="">
            </div>
            <div class="m-5">
                <h4 class="text-center">{{ username }}</h4>
            </div>
        </div>
        <div class="col col-12 d-flex ">
            <div class="row w-100 d-flex justify-content-around">
                <div class="col col-3 rounded border border-1 border-success ">
                    <h5 class="text-center m-2">Top Wim</h5>
                    <p class="text-center fs-4"><img src="/storage/radianite.png" class="currency-icon">10312212313123</p>
                </div>
                <div class="col col-4 rounded border border-1 border-success ">
                    <h5 class="text-center m-2">Wager</h5>
                    <p class="text-center fs-4">{{ wager }}</p>
                </div>
                <div class="col col-3 rounded border border-1 border-success ">
                    <h5 class="text-center m-2">Crates Opened</h5>
                    <p class="text-center fs-4">12</p>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
    const prop = defineProps(["username", "profileImage", "wager"])
</script>
