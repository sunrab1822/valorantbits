<template>
    <div class="modal fade" id="registerModal" tabindex="-1" data-bs-theme="dark">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">Register</div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <label for="username" class="col-md-4 col-form-label text-md-end">Username</label>

                        <div class="col-md-6">
                            <input id="username" type="text" class="form-control" name="username" v-model="username" required autocomplete="username" autofocus :class="{ 'is-invalid': validation.username }">
                            <span class="invalid-feedback" role="alert" v-if="validation.username">
                                <strong>{{ validation.username }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="email" class="col-md-4 col-form-label text-md-end">Email Address</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control" name="email" v-model="email" required autocomplete="email" :class="{ 'is-invalid': validation.email }">
                            <span class="invalid-feedback" role="alert" v-if="validation.email">
                                <strong>{{ validation.email }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control" name="password" v-model="password" required autocomplete="new-password" :class="{ 'is-invalid': validation.password }">
                            <span class="invalid-feedback" role="alert" v-if="validation.password">
                                <strong>{{ validation.password }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Confirm Password</label>

                        <div class="col-md-6">
                            <input id="password-confirm" type="password" class="form-control" v-model="password_confirmation" name="password_confirmation" required autocomplete="new-password" :class="{ 'is-invalid': validation.password_confirmation }">
                            <span class="invalid-feedback" role="alert" v-if="validation.password_confirmation">
                                <strong>{{ validation.password_confirmation }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary" @click="register">
                                Register
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import axios from 'axios';
    import * as bootstrap from 'bootstrap';
    import { ref } from 'vue';
    import { useUserStore } from '@stores/user';

    const userStore = useUserStore();

    let username = ref("");
    let email = ref("");
    let password = ref("");
    let password_confirmation = ref("");

    let validation = ref({
        username: null,
        email: null,
        password: null,
        password_confirmation: null
    });

    async function register() {
        let request = await axios.post("/api/register", {
            username: username.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        });

        if(!request.data.error) {
            if(request.data.data != null) {
                userStore.setUser(request.data.data);
                userStore.setLoggedIn(true);
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('registerModal')).hide();
            username.value = "";
            email.value = "";
            password.value = "";
            password_confirmation.value = "";
        } else {
            if(request.data.error_type == "validation") {
                for(const key of Object.keys(request.data.data)) {
                    validation.value[key] = request.data.data[key][0];
                }
            }
        }
    }
</script>
