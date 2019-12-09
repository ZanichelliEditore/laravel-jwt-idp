<template>
    <div>
        <div class="d-flex mb-2 mt-2 align-items-center justify-content-between">
            <div>
                <h1>Lista dei clients</h1>
            </div>
            <div class="col-4 input-group px-0">
                <input v-model="filterClientInput" type="text"
                        class="form-control" aria-label="Search input filter" placeholder="Filtra per nome o ruolo">
            </div>
        </div>
        <div class="table-responsive min-height">
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">OAuth Client Id</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Ruoli</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="loading">
                    <th colspan="7" class="px-0 py-0">
                        <div class="progress" style="height: 5px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75"
                                    aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                        </div>
                    </th>
                </tr>
                <oauth-row v-for="client in pagination.data" :oauth_client_id="client.oauth_client_id" :oauth_name="client.oauth_name" :oauth_roles="client.roles" :loadClients="(page) => loadClients(page = pagination.meta.current_page)" :key="client.oauth_client_id" :roles="roles"></oauth-row>
                <tr v-if="!loading && pagination.data.length === 0">
                    <td colspan="7" class="text-center">
                        Nessun client trovato
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <paginator v-if="pagination.meta.total > pagination.meta.per_page" :pagination="pagination.meta" :onChangePage="page => loadClients(page)"></paginator>
    </div>
</template>

<script>
    import { EventBus } from "../event-bus";
    import OauthClientRow from "./OauthClientRow";
    import Paginator from './Paginator';
        
    export default {
        
        components: {
            "oauth-row": OauthClientRow,
            "paginator": Paginator
        },

        data() {
            return {
                pagination: {        
                    data: [],
                    meta: {
                        current_page: -1,
                        total: 0,
                        last_page: 0,
                        per_page: 10
                    }    
                },
                loading: false,
                filterClientInput: null,
                roles: []
            }
        },

        created() {
            this.loadClients();
            this.getClientRoles();
            EventBus.$on('newClient', notification => {
                this.loadClients(-1);
            });
        },

        watch: {
            filterClientInput: function(newValue, oldValue) {
                this.filterClients();
            },
        },

        methods: {

            loadClients(page = 1) {
                
                this.loading = true;
                axios.get('/admin/oauth-clients-all', {
                    params: {
                        page: page,
                        q: this.filterClientInput
                    }
                }).then(response => {
                    if (page == -1) {
                        var lastPage = response.data.meta.last_page;
                        return this.loadClients(lastPage);
                    }
                    this.pagination = response.data
                    this.loading = false;
                }).catch(error => {
                    this.loading = false;
                    EventBus.$emit('newNotification', {
                        message: 'Errore durante il caricamento dei clients',
                        type: 'ERROR'
                    });
                });
            },

            filterClients() {
                this.loadClients();
            }, 

            getClientRoles(){
            axios.get('/v1/client-roles', {})
                 .then(response =>  {
                    this.roles = response.data;
          
                    
                    
            }).catch(error =>  {
                EventBus.$emit('newNotification', {
                    message: 'Errore durante il recupero dei client Roles',
                    type: 'ERROR'
                });
            })

        },
        }
    }
</script>

<style scoped>
    .min-height {
       min-height: 200px !important; 
    }
</style>