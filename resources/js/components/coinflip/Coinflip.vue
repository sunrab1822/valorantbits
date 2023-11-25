<template>
    <div class="d-flex p-1 justify-content-center">
        <div class="m-1">
            <input type="text" class="form-control" placeholder="Price" v-model="bet_amount">
        </div>
        <div class="m-1">
            <button class="btn btn-secondary">+1</button>
        </div>
        <div class="m-1">
            <button class="btn btn-secondary">+10</button>
        </div>
        <div class="m-1">
            <button class="btn btn-secondary">Min</button>
        </div>
        <div class="m-1">
            <button class="btn btn-secondary">Max</button>
        </div>
        <div class="m-1 ms-3">
            <button class="btn btn-success">Place Bet</button>
        </div>
    </div>
    <div class="bg-dark mx-auto rounded p-2 ">
        <div class="d-flex justify-content-evenly align-items-center">
            <div>
                <div class="user_side_heads">
                    <img class="flip-profile-picture" src="/storage/crate_images/crate_yellow.png" alt="">
                </div>
                <button class="btn btn-danger d-flex mx-auto" @click="join('heads')">Heads</button>
            </div>
            <div id="coin">
                <div class="side-a">
                    <img class="w-100" src="/storage/crate_images/crate_blue.png" alt="">
                </div>
                <div class="side-b">
                    <img class="w-100" src="/storage/crate_images/crate_red.png" alt="">
                </div>
            </div>
            <div class="user_side_tails">
                <img class="flip-profile-picture" src="/storage/crate_images/crate_green.png">
                <button class="btn btn-danger d-flex mx-auto" @click="join('tails')">Tails</button>
            </div>
        </div>
    </div>


</template>

<script setup>
    import axios from 'axios';
    import { useRoute } from 'vue-router';
    import { ref } from 'vue';

    let bet_amount = ref(1);
    const route = useRoute();
    window.Echo.private('App.Models.Coinflip.' + route.params.id).listen(".coinflip-join", playerJoined);

    async function join(bet_side) {
        await axios.post("/api/coinflip/join", {
            game_id: route.params.id,
            bet_side: bet_side,
            bet_amount: bet_amount.value
        });
    }

    function playerJoined(data) {
        $('.user_side_' + data.side).find("img").attr("src", data.user_profile);
        var flipResult = Math.random();
        $('#coin').removeClass();
        setTimeout(function(){
            if(flipResult <= 0.5){
                $('#coin').addClass('heads');
                console.log('it is head');
            } else {
                $('#coin').addClass('tails');
                console.log('it is tails');
            }
        }, 1500);
    }
</script>

<style scoped>
.flip-profile-picture{
    height: 10rem;
    width: 10rem;
}

#coin {
  position: relative;
  width: 300px;
  height: 300px;
  cursor: pointer;
}
#coin div {
  width: 100%;
  height: 100%;
  -webkit-border-radius: 50%;
     -moz-border-radius: 50%;
          border-radius: 50%;
  -webkit-box-shadow: inset 0 0 45px rgba(255,255,255,.3), 0 12px 20px -10px rgba(0,0,0,.4);
     -moz-box-shadow: inset 0 0 45px rgba(255,255,255,.3), 0 12px 20px -10px rgba(0,0,0,.4);
          box-shadow: inset 0 0 45px rgba(255,255,255,.3), 0 12px 20px -10px rgba(0,0,0,.4);
    position: absolute;
  -webkit-backface-visibility: hidden;
}
.side-a {
  background-color: #bb000050;
  padding: 50px;
}
.side-b {
  background-color: #3e3e3e;
  padding: 50px;
}

#coin {
  transition: -webkit-transform 1s ease-in;
  -webkit-transform-style: preserve-3d;
}
.side-a {
  z-index: 100;
}
.side-b {
  -webkit-transform: rotateY(-180deg);

}

#coin.heads {
  -webkit-animation: flipHeads 3s ease-out forwards;
  -moz-animation: flipHeads 3s ease-out forwards;
    -o-animation: flipHeads 3s ease-out forwards;
       animation: flipHeads 3s ease-out forwards;
}
#coin.tails {
  -webkit-animation: flipTails 3s ease-out forwards;
  -moz-animation: flipTails 3s ease-out forwards;
    -o-animation: flipTails 3s ease-out forwards;
       animation: flipTails 3s ease-out forwards;
}

@-webkit-keyframes flipHeads {
  from { -webkit-transform: rotateY(0); -moz-transform: rotateY(0); transform: rotateY(0); }
  to { -webkit-transform: rotateY(1800deg); -moz-transform: rotateY(1800deg); transform: rotateY(1800deg); }
}
@-webkit-keyframes flipTails {
  from { -webkit-transform: rotateY(0); -moz-transform: rotateY(0); transform: rotateY(0); }
  to { -webkit-transform: rotateY(1980deg); -moz-transform: rotateY(1980deg); transform: rotateY(1980deg); }
}

</style>