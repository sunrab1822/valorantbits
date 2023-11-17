<template>
    <div class="d-flex justify-content-between mb-4">
        <div class="d-flex">
            <img class="profile-picture border border-success rounded-circle" :src="userObj.profile_image" alt="">
            <div class="mt-1 ms-3">
                <div class="d-flex">
                    <h4>{{ userObj.username }}</h4>
                </div>
                <div class="text-secondary">Joined {{ joined_date }}</div>
            </div>
        </div>
        <div class="mt-1">
            <h5 class="text-secondary">UID: {{ userObj.id }}</h5>
        </div>
    </div>
    <div class="mb-4">
        <div class="progress bg-dark" style="height: 0.75rem;">
            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 75%"></div>
        </div>
        <div class="d-flex justify-content-between mt-1">
            <div>Level 1</div>
            <div class="text-secondary">750/1000 XP</div>
            <div>Level 2</div>
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

</template>

<script setup>
    import { ref } from 'vue';
    const props = defineProps(["user"]);

    let userObj = JSON.parse(props.user);
    let openPartial = ref("details");

    let joined_date = new Date(userObj.created_at).toLocaleDateString("en-us", { year: 'numeric', month: 'long', day: 'numeric'});

    $(function(){
        $(".partial_selector").on('click', function(){
            openPartial.value = $(this).data("partial");
        });
    });
</script>
