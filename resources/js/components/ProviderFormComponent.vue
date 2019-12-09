<template>
    <div>
        <div id="create-provider-container" class="pb-4">

            <div class="d-flex flex-column flex-lg-row justify-content-start justify-content-lg-between align-items-lg-center mb-5">
                <div>
                    <h1>Crea nuovo provider</h1>
                </div>
                <div>
                    <button type="submit" class="btn btn-outline-dark">Visualizza tutti</button>
                </div>
            </div>

            <form v-on:submit.prevent="submit">
                <div class="form-row">
                    <div class="form-group col-lg-6">
                        <label for="input-domain">Dominio</label>
                        <input type="text" v-bind:class="'form-control ' + (!validator.domain ? 'is-invalid' : '')"
                               id="input-domain" placeholder="zte.zanichelli.it" name="domain" v-model="form.domain">
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="input-logout-url">Logout URL</label>

                        <input type="text" class="form-control"
                               id="input-logout-url" :placeholder="'https://' + (form.domain ? form.domain : 'dominio') + '/logout-idp'" name="logoutUrl" v-model="form.logoutUrl">
                        <small>Se vuoto il valore è quello di default</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-lg-6">
                        <label for="input-username">Username Basic Auth</label>
                        <input type="text" :class="'form-control ' + (!validator.username ? 'is-invalid' : '')"
                               id="input-username" placeholder="Username" name="username" v-model="form.username">
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="input-password">Password Basic Auth</label>
                        <input type="password" :class="'form-control ' + (!validator.password ? 'is-invalid' : '')"
                               id="input-password" placeholder="Secret" name="password" v-model="form.password">
                        <small>Min 5 caratteri</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Crea Provider</button>
            </form>
        </div>

    </div>
</template>

<script>
    import { EventBus } from "../event-bus";

    export default {

        data() {
            return {
                form: {
                    domain: null,
                    username: null,
                    password: null,
                    logoutUrl: null
                },
                validator: {
                    domain: true,
                    username: true,
                    password: true,
                    logoutUrl: true
                }
            }
        },

        methods: {

            submit: function () {

                if (!this.validate()) {
                    return;
                }

                let vm = this;
                axios.post('/admin/providers', {
                    domain: vm.form.domain,
                    logoutUrl: vm.form.logoutUrl,
                    username: vm.form.username,
                    password: vm.form.password,
                }).then(function (data) {
                    vm.resetForm();
                    EventBus.$emit('newNotification', {
                        message: 'Provider aggiunto correttamente',
                        type: 'SUCCESS'
                    });
                }).catch(function (error) {
                    
                    EventBus.$emit('newNotification', {
                        message: 'Dominio esistente o errore durante la registrazione del provider.',
                        type: 'ERROR'
                    });
                })
            },

            validate: function() {

                this.validator.domain = !!this.form.domain;
                this.validator.username = !!this.form.username;
                this.validator.password = !!this.form.password && this.form.password.length > 4;


                if (!this.form.domain || !this.form.username || !this.form.password || this.form.password.length < 5) {
                    return false;
                }

                return true;
            },

            resetForm: function () {
                this.form.domain = null;
                this.form.username = null;
                this.form.password = null;
                this.form.logoutUrl = null;
            }
        }
    }
</script>