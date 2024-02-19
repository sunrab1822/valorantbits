<template>
    <div class="d-flex justify-content-between mb-4" v-if="user">
        <div class="d-flex">
            <img class="profile-picture border border-success rounded-circle" :src="user.profile_image" data-bs-toggle="modal" data-bs-target="#changePictureModal">
            <div class="mt-1 ms-3">
                <div class="d-flex">
                    <h4>{{ user.username }}</h4>
                </div>
                <div class="text-secondary">Joined {{ joined_date }}</div>
            </div>
        </div>
        <div class="mt-1">
            <h5 class="text-secondary">UID: {{ user.id }}</h5>
        </div>
    </div>
    <div class="mb-4" v-if="user">
        <div class="progress bg-nav-dark" style="height: 0.75rem;">
            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" :style="{ 'width': progressbar_width + '%' }"></div>
        </div>
        <div class="d-flex justify-content-between mt-1">
            <div>Level {{ current_level }}</div>
            <div class="text-secondary">{{ user.experience }}/{{ required_xp }} XP</div>
            <div>Level {{ current_level + 1 }}</div>
        </div>
    </div>
    <div class="divider"></div>
    <div class="row">
        <div class="col-md-2">
            <div class="card vb-card mb-2 fs-5 p-2 text-white partial_selector" data-partial="details" :class="{'selected': openPartial == 'details'}">
                Profile
            </div>
            <div class="card vb-card mb-2 fs-5 p-2 text-white partial_selector" data-partial="history" :class="{'selected': openPartial == 'history'}">
                History
            </div>
            <div class="card vb-card fs-5 p-2 text-white partial_selector" data-partial="settings" :class="{'selected': openPartial == 'settings'}">
                Settings
            </div>
        </div>

        <div class="col-md-10">
            <profile-details v-if="openPartial == 'details'"></profile-details>
            <profile-history v-else-if="openPartial == 'history'"></profile-history>
            <profile-settings v-else-if="openPartial == 'settings'"></profile-settings>
        </div>
    </div>
    <change-profil-img :images="images"/>
</template>

<script setup>
    import { ref } from 'vue';
    import { useUserStore } from '@stores/user';

    const userStore = useUserStore();
    const props = defineProps(["id"]);

    let user = userStore.user;
    let joined_date = ref();
    let openPartial = ref("details");
    let required_xp = ref(1000);
    let progressbar_width = ref(0);
    let current_level = ref(0);
    let images = ref();

    (async function() {
            joined_date.value = new Date(user.created_at).toLocaleDateString("en-us", { year: 'numeric', month: 'long', day: 'numeric'})
            current_level.value = (user.experience - 1000 < 0) ? 0 : Math.ceil((user.experience - 1000) / 250);
            required_xp.value = 1000 + ((current_level.value) * 250);
            progressbar_width.value = (user.experience / required_xp.value) * 100;

            console.log((user.experience / required_xp.value) * 100, required_xp.value);

    })();

    $(function(){
        $(".partial_selector").on('click', function(){
            openPartial.value = $(this).data("partial");
        });
    });

    (async function () {
        let request = await axios.get("/api/images");
        images.value = request.data;
    })();
</script>
