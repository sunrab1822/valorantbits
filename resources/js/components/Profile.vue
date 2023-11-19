<template>
    <div class="d-flex justify-content-between mb-4">
        <div class="d-flex">
            <img class="profile-picture border border-success rounded-circle" :src="userObj.profileImage" alt="">
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
    <div class="d-flex">
        <div class="row w-100 d-flex justify-content-around">
            <div class="col col-3 rounded border border-1 border-success">
                <h5 class="text-center m-2">Top Win</h5>
                <p class="text-center fs-4"><img src="/storage/radianite.png" class="currency-icon">10312212313123</p>
            </div>
            <div class="col col-4 rounded border border-1 border-success">
                <h5 class="text-center m-2">Wager</h5>
                <p class="text-center fs-4">{{ 10 }}</p>
            </div>
            <div class="col col-3 rounded border border-1 border-success">
                <h5 class="text-center m-2">Crates Opened</h5>
                <p class="text-center fs-4">12</p>
            </div>
        </div>
    </div>
</template>

<script setup>
    const props = defineProps(["user"]);

    let userObj = JSON.parse(props.user);

    let joined_date = new Date(userObj.created_at).toLocaleDateString("en-us", { year: 'numeric', month: 'long', day: 'numeric'});
</script>
