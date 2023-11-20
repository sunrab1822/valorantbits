<template>
    <div class="modal fade" id="loginModal" tabindex="-1" data-bs-theme="dark">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Login</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <label for="username" class="col-md-4 col-form-label text-md-end">Username</label>

                        <div class="col-md-6">
                            <input id="login-username" type="text" class="form-control" name="username" required autocomplete="username" autofocus :class="{'is-invalid': validation.username}" v-model="username">
                            <span class="invalid-feedback" role="alert" v-if="validation.username">
                                <strong>{{ validation.username }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                        <div class="col-md-6">
                            <input id="login-password" type="password" class="form-control" name="password" required autocomplete="current-password" :class="{'is-invalid': validation.password}" v-model="password">
                            <span class="invalid-feedback" role="alert" v-if="validation.password">
                                <strong>{{ validation.password }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 offset-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="login-remember" v-model="remember">

                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary" @click="login">
                                Login
                            </button>

                            <a class="btn btn-link" href="">
                                Forgot Your Password?
                            </a>
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

    const emit = defineEmits(['isLoggedIn']);

    let username = ref("");
    let password = ref("");
    let remember = ref(false);
    let validation = ref({
        username: null,
        password: null
    });

    async function login() {
        for(const key of Object.keys(validation.value)) {
            validation.value[key] = null;
        }

        let request = await axios.post("/api/login", {
            username: username.value,
            password: password.value,
            remember: remember.value
        });

        if(!request.data.error) {
            emit('isLoggedIn', request.data.data);
            localStorage.setItem("isLoggedIn", request.data.data);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('loginModal')).hide();
            username.value = "";
            password.value = "";
        } else {
            if(request.data.error_type == "validation") {
                for(const key of Object.keys(request.data.data)) {
                    validation.value[key] = request.data.data[key][0];
                }
            }
        }
    }
</script>
