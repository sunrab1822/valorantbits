<template>
    <div class="modal fade" id="loginModal" tabindex="-1" data-bs-theme="dark">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Login</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <label for="username" class="col-md-4 col-form-label text-md-end">Username</label>

                        <div class="col-md-6">
                            <input id="login-username" type="text" class="form-control" name="username" value="" required autocomplete="username" autofocus>

                            <!-- @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                        <div class="col-md-6">
                            <input id="login-password" type="password" class="form-control " name="password" required autocomplete="current-password">

                            <!-- @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror -->
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 offset-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="login-remember">

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

    const emit = defineEmits(['isLoggedIn']);

    async function login() {
        let request = await axios.post("/api/login", {
            username: $("#login-username").val(),
            password: $("#login-password").val(),
            remember: $("#login-remember").is(":checked")
        });

        if(!request.data.error) {
            emit('isLoggedIn', request.data.data);
            localStorage.setItem("isLoggedIn", request.data.data);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('loginModal')).hide();
        }
    }
</script>
