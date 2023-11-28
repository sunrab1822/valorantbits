<template>
    <div class="col px-1">
        <div class="card justify-content-center crate-item" :class="[tier.toLowerCase()+'-background']" style="height: 175px !important;">
            <div class="d-flex flex-column align-items-center py-1 h-100">
                <div class="text-center px-2 "><div class="fs-5">{{ name }}</div></div>
                <div class="d-flex align-items-center flex-grow-1 my-2">
                    <img style="max-width: 140px;" :src="image">
                </div>
                <div class="item-details d-flex justify-content-between px-3" style="width: 100%;">
                    <div class="fs-6-1">{{ chance }}%</div>
                    <div class="fs-6-1"><currency />{{ price.toBalance(2) }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    defineProps(['name', 'image', 'chance', 'price', 'tier']);
</script>
