<template>
       <div class="card col-md-4 crate-item pt-0">
        <div class="card-img-overlay d-flex align-items-start justify-content-center">
            <p>{{ name }}</p>
        </div>
            <div class="card-img-overlay d-flex justify-content-between align-items-end ">

                    <p>{{ chance }}%</p>
                    <div>
                        <p><img src="/storage/radianite.png" class="currency-icon">300</p>
                    </div>
            </div>
            <img class="card-img " :src="image">
        </div>
</template>

<script setup>
    defineProps(['name', 'image','chance'])
</script>
