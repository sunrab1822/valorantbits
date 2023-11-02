<template>

    <div class="row">
        <div class="col">
            <button class="btn btn-success" >&lsaquo;  Back</button>
        </div>
        <div class="col text-center">
            <h1 class="">{{ crateObj.name }}</h1>
        </div>
        <div class="col"></div>
    </div>

    <div class="d-flex justify-content-center ">
        <div class="bg-secondary rounded me-2">
            <button class="btn">1</button>
            <button class="btn">2</button>
            <button class="btn">3</button>
            <button class="btn">4</button>
            <button class="btn">5</button>
            <button class="btn">More</button>
        </div>
        <div>
            <button class="btn btn-success">Open</button>
        </div>
    </div>
    <div>
        <h3>Crate items</h3>
        <div class="row">
            <CrateItem v-for="skin in crateObj.contents" :name="skin.name" :uuid="skin.uuid"></CrateItem>
        </div>
    </div>


</template>

<script setup>
const prop = defineProps(['crate'])

 let crateObj = JSON.parse(prop.crate)
    console.log(crateObj);


</script>
