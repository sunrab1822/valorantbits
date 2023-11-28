<template>
    <div class="account-heading">
        Account
    </div>
    <div class="row row-cols-3 mb-4">
        <div class="col pe-3">
            <div class="rounded py-1 profile-card">
                <h5 class="text-center m-2">Accumulated</h5>
                <p class="text-center fs-4"><currency />10312212313123</p>
            </div>
        </div>
        <div class="col px-3">
            <div class="rounded py-1 profile-card">
                <h5 class="text-center m-2">Wagered</h5>
                <p class="text-center fs-4"><currency />{{ wager.toBalance(2) }}</p>
            </div>
        </div>
        <div class="col ps-3">
            <div class="rounded py-1 profile-card">
                <h5 class="text-center m-2">Profit</h5>
                <p class="text-center fs-4"><currency />{{ profit.toBalance(2) }}</p>
            </div>
        </div>
    </div>
    <div class="divider"></div>
    <div class="profile-heading">
        Game Stats
    </div>
    <div class="row row-cols-3">
        <div class="col pe-3">
            <div class="rounded py-1 profile-card">
                <h5 class="text-center m-2">Most Played Game</h5>
                <p class="text-center fs-4">{{ most_played_game }}</p>
            </div>
        </div>
        <div class="col px-3">
            <div class="rounded py-1 profile-card">
                <h5 class="text-center m-2">Most Won Game</h5>
                <p class="text-center fs-4">{{ most_won_game }}</p>
            </div>
        </div>
        <div class="col ps-3">
            <div class="rounded py-1 profile-card">
                <h5 class="text-center m-2">Highest Win</h5>
                <p class="text-center fs-4"><currency />{{ highest_win.toBalance(2) }}</p>
            </div>
        </div>
    </div>
    <div class="divider"></div>
    <div class="profile-heading">
        Profit Chart
    </div>
    <div class="">
        <select name="profit_chart_select" @change="updateChart">
            <option v-for="option in profit_chart_select_options" :value="option.id" :selected="option.id == 'all'">{{ option.name }}</option>
        </select>
    </div>
    <canvas id="profit_chart"></canvas>
</template>

<script setup>
    import axios from 'axios';
    import { ref, onMounted } from 'vue';
    import Chart from 'chart.js/auto';

    Chart.defaults.color = '#838585';

    let wager = ref(0);
    let highest_win = ref(0);
    let most_won_game = ref("");
    let most_played_game = ref("");
    let profit = ref(0);
    let profit_chart = null;
    let profit_chart_select_options = ref([
        {
            id: "all",
            name: "All time"
        },
        {
            id: "30",
            name: "30 days"
        },
        {
            id: "7",
            name: "7 days"
        },
        {
            id: "1",
            name: "1 day"
        }
    ]);


    getUserProfileDetails();

    const down = (ctx, value) => ctx.p0.parsed.y > ctx.p1.parsed.y ? value : undefined;

    async function getUserProfileDetails() {
        let profile_details = await axios.get("/api/user/profile");
        if(!profile_details.data.error) {
            wager.value = Number(profile_details.data.data.wagered);
            highest_win.value = Number(profile_details.data.data.top_win);
            most_won_game.value = profile_details.data.data.most_won_game;
            most_played_game.value = profile_details.data.data.most_played_game;
            profit.value = profile_details.data.data.profit;
            let profit_data = profile_details.data.data.profit_data.map(function(obj) {
                return obj.profit_amount.toBalance(2);
            });

            let array_keys = Object.keys(profit_data);
            array_keys.shift();
            array_keys.push(profit_data.length);

            let config = {
                type: 'line',
                data: {
                    labels: array_keys,
                    datasets: [{
                        label: "Profit",
                        data: profit_data,
                        fill: false,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.3,
                        pointBackgroundColor: function(context) {
                            var index = context.dataIndex;
                            var value = context.dataset.data[index];
                            return value < 0 ? 'red' : 'green';
                        },
                        pointBorderColor: function(context) {
                            var index = context.dataIndex;
                            var value = context.dataset.data[index];
                            return value < 0 ? 'red' : 'green';
                        },
                        segment: {
                            borderColor: ctx => down(ctx, 'rgba(255,26,104,1)') || '#198754'
                        }
                    }]
                },
                options: {
                    scales: {
                        y: {
                            stacked: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            };

            profit_chart = new Chart("profit_chart", config);
        }
    }

    async function updateChart() {
        let chart_data = await axios.post("/api/user/profile/profit-chart", {
            days: $("select[name='profit_chart_select']").find(":selected").val()
        });

        if(profit_chart !== null) {
            let profit_data = chart_data.data.data.map(function(obj) {
                return obj.profit_amount.toBalance(2);
            });
            let array_keys = Object.keys(profit_data);
            array_keys.shift();
            array_keys.push(profit_data.length);
            profit_chart.data.labels = array_keys;
            profit_chart.data.datasets[0].data = profit_data;
            profit_chart.update();
        }
    }
</script>
