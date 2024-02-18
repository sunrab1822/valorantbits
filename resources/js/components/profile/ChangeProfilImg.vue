<template>
<div class="modal fade" id="changePictureModal" data-bs-theme="dark" aria-labelledby="changePictureLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-4" id="changePictureLabel">Change Profil Picture</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row row-cols-6">
                <img class="p-2 image" v-for="(image,key) in images" :style="{border: selectedUrl == image ? '1px solid red' : 'none'}" :src="'/storage/'+image" @click="selectimg(image)">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" @click="saveProfileImage">Choose Image</button>
      </div>
    </div>
  </div>
</div>


</template>

<script setup>
import { ref } from 'vue';
    defineProps(['images'])
    import { useUserStore } from '@stores/user';

    const userStore = useUserStore();
    let selectedUrl = ref()

    function selectimg(img){

        selectedUrl.value = img;
        // e.target.style.border = "1px solid red";
    }

    function saveProfileImage(){

        if (selectedUrl.value){
            axios.post("/api/user/save-image", {url : "/storage/"+selectedUrl.value});
            userStore.user.profile_image = "/storage/"+selectedUrl.value;
        }
    }




</script>
