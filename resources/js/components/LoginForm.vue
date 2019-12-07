<template>
    <div>
        <div class="col-10 col-sm-8 col-md-6 col-lg-4 col-xl-3 mr-auto ml-auto border px-3 py-4 mt-5">
            <img class="w-100 mb-4" src="/images/logo.png">
            <form method="post" action="/v2/login">
                <input type="hidden" name="redirect" value="placeholder-redirect">
                <div class="form-group">
                    <label for="inputUsername">Username</label>
                    <input v-model="username" id="inputUsername" type="text" class="form-control"  name="username" placeholder="Username">
                </div>
                <div class="form-group">
                    <label for="inputPassword">Password</label>
                    <input v-model="password" id="inputPassword" type="password" class="form-control" name="password" placeholder="Password">
                </div>
                <button type="submit" :class="['btn btn-primary mr-auto ml-auto d-block w-50 mt-4 text-white']" :disabled="!validate" @click.prevent="login()">Login</button>
            </form>
        </div>

        <div v-if="errorMessage !== null" class="alert alert-danger col-10 col-sm-8 col-md-6 col-lg-4 col-xl-3 mr-auto ml-auto mt-4">
            <ul class="mb-0">
                <li>{{ errorMessage }}</li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {

        props: {
            redirect: null
        },

        data() {
            return {
                username: null,
                password: null,
                errorMessage: null
            }
        },

        computed: {

            validate() {
                return this.username && this.password && this.password.length > 2;
            }
        },

        methods: {

            login() {

                let vm = this;

                if (!this.validate) {
                    return;
                }

                axios.post('/v2/login', {
                    username: vm.username,
                    password: vm.password
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (vm.redirect) {
                        window.location.href = vm.redirect + "?token=" + response.data.token;
                    } else {
                        window.location.href = '/';
                    }
                }).catch(error => {
                    if (error.response) {
                        vm.errorMessage = error.response.data.message;
                    } else {
                        vm.errorMessage = 'Errore inaspettato';
                    }
                });
            }
        }

    }
</script>

<style scoped>

</style>