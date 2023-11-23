<template>
    <router-link class="no-style" :to="{name:'coinflip_play', params:{id:1}}">
        <div class="card col m-2 card-battle bg-dark">
            <div class="card-body text-white">
                <div class="">
                    <h4 class="card-title text-center no-wrap">Coinflip 1</h4>
                </div>
                <div class="d-flex justify-content-center my-2">
                    <img class="card-img" :src="image" style="max-width: 120px; max-height: 120px;">
                </div>
                <div class="d-flex justify-content-center">
                    <img src="/storage/radianite.png" class="currency-icon" alt="">
                    <div class="fs-5 fw-semibold">1500.00</div>
                </div>
            </div>
        </div>
    </router-link>
</template>

<script setup>

function Join() {

}

$(function(){

$('#coin').on('click', function(){
  var flipResult = Math.random();
  $('#coin').removeClass();
  setTimeout(function(){
    if(flipResult <= 0.5){
      $('#coin').addClass('heads');
      console.log('it is head');
    }
    else{
      $('#coin').addClass('tails');
      console.log('it is tails');
    }
  }, 100);
});
});



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
