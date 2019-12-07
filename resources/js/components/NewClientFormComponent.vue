<template>
    <div>
        <div class="pb-4">

            <div class="d-flex flex-column flex-lg-row justify-content-start justify-content-lg-between align-items-lg-center mb-5">
                <form v-on:submit.prevent="" class="col-12">
                    <h1>Crea nuovo Client</h1>
                    <div class="form-group">
                        <label for="client-name">Nome Cliente</label>
                        <input v-model="form.name" id="client-name" type="text" :class="'form-control col-sm-12 col-md-8 col-lg-6' + (!validator.name ? ' is-invalid' : '')"  name="client-name" placeholder="Inserisci un nome">
                    </div>
                    <div class="form-group">
                        <label for="redirectURL">URL Callback</label>
                        <input v-model="form.redirectURL" id="redirectURL" type="text" :class="'form-control col-sm-12 col-md-8 col-lg-6' + (!validator.redirectURL ? ' is-invalid' : '')" name="redirectURL" placeholder="Inserisci una rotta di callback" >
                    </div>
                    <div class="d-flex">
                        <button class="btn btn-primary confirm-button mt-2" @click="createClient" :disabled="loading"> Crea client </button>
                        <p v-show="loading" class="align-self-end m-0 ml-2"><i class="fas fa-spinner fa-pulse mr-2"></i>Elaborazione in corso...</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from "../event-bus";

    export default {

        data() {
            return {
               form: {
                    name: null,
                    redirectURL: null,
                },
                validator: {
                    name: true,
                    redirectURL: true
                },
                loading: false,
            }
        },

        methods: {

            validate: function() {

                this.validator.name = !!this.form.name;
                this.validator.redirectURL = !!this.form.redirectURL;

                if (!this.form.name || !this.form.redirectURL ) {
                    return false;
                }
                return true;
            },

            createClient(){

                if (this.validate()) { 
                    this.loading = true;
                    const data = {
                        name: this.form.name,
                        redirect: this.form.redirectURL
                    };
                    axios.post('/oauth/clients', data)
                        .then(response => {
                            this.exportCSVFile(response.data);
                            
                            EventBus.$emit('newNotification', {
                                message: 'Nuovo client creato correttamente',
                                type: 'SUCCESS'
                            });
                            EventBus.$emit('newClient', {});
                            this.resetForm();
                            this.loading = false;
                        })
                        .catch (response => {
                            EventBus.$emit('newNotification', {
                                message: 'Errore nella creazione del client',
                                type: 'ERROR'
                            });
                            this.loading = false;
                        });
                }
            },

            exportCSVFile(jsonObject) {
                // headers list in display order
                var headers = [
                    'id',
                    'name',
                    'secret',
                    'redirect'
                ];

                // create object filtered by heders
                var values = [];
                headers.forEach(key => {
                    if (jsonObject.hasOwnProperty(key)) {
                        values.push(jsonObject[key]);
                    } else {
                        values.push(" ");
                    }
                });

                // create CSV String
                var csv = headers.join(',') + '\r\n' + 
                          values.join(',');
                
                var exportedFilename = jsonObject.name.replace(/\s/g, "_") + '.csv';

                // create and download file 
                var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                if (navigator.msSaveBlob) { // IE 10+
                    navigator.msSaveBlob(blob, exportedFilenmae);
                } else {
                    var link = document.createElement("a");
                    if (link.download !== undefined) { // feature detection
                        // Browsers that support HTML5 download attribute
                        var url = URL.createObjectURL(blob);
                        link.setAttribute("href", url);
                        link.setAttribute("download", exportedFilename);
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                }
            },

            resetForm: function () {
                this.form.name = null;
                this.form.redirectURL = null;
            }
        }
    }
</script>