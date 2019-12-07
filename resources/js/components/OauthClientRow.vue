<template>
    <tr>
        <td>{{ oauth_client_id }}</td>                
        <td>{{ oauth_name }}</td>
        <td class="d-flex justify-content-between">{{ oauth_roles.length > 0 ? oauth_roles.join(', ') : "Nessun ruolo assegnato" }}
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Modifica ruolo
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <div class="form-group form-check ml-2 select-all">
                        <input :checked="checked" type="checkbox" class="form-check-input checkmark" @click="selectAllfn">
                        <label class="form-check-label font-weight-bold ml-2">Seleziona tutti</label>
                    </div>
                    <div v-for="role in roles" :key="role" class="form-group form-check ml-2">
                        <input v-if="oauth_roles.some(el => el == role)" type="checkbox" class="form-check-input checkmark" :value="role" v-model="newRoles" ref="precheck"> 
                        <input v-else type="checkbox" class="form-check-input checkmark" :value="role" v-model="newRoles"> 
                        <label class="form-check-label ml-2">{{role}}</label>
                    </div>
                    <button @click="changeRoles(oauth_client_id)" type="button" class="btn btn-dark ml-2">Conferma</button>
                </div>
            </div>
        </td>
    </tr>
</template>

<script>
import { EventBus } from '../event-bus';

export default {

    name: "OauthClientRow",

    props: {
        oauth_client_id: {
            required: true
        },
        oauth_name: {
            required: true,
            type: String
        },
        oauth_roles: {
            required: true,
            type: Array
        },  
        loadClients: {
            required: true,
            type: Function
        },
        roles: {
            required: true,
            type: Array
        }

    },

    data() {

        return {
            newRoles: [],
            selectAll: false,
            checked: false
        }
    },
    
    mounted() { 
        
        let prechecked = this.$refs.precheck;
    
        if(prechecked) {
            prechecked.forEach(el => this.newRoles.push(el.value));
            if (this.newRoles.length == this.roles.length ) {
                this.checked = true;
                this.selectAll = true;
            }
        } 
    },

    watch: {
        newRoles: function (newValue, oldValue) {
            
            if (this.newRoles.length == this.roles.length ) {
                this.checked = true;
                this.selectAll = true;
            } else {
                this.checked = false;
                this.selectAll = false;
            }
        }
    },

    methods: {

        changeRoles(clientId) {

            axios.put('/admin/update-roles', {
                clientId: clientId,
                roles: this.newRoles             
            }).then(data =>  {
                this.loadClients()
                EventBus.$emit('newNotification', {
                    message: 'Ruoli aggiornati correttamente',
                    type: 'SUCCESS'
                });
            }).catch(error =>  {
                EventBus.$emit('newNotification', {
                    message: 'Errore durante la modifica dei ruoli',
                    type: 'ERROR'
                });
            })
        },

        selectAllfn() {
            this.selectAll = !this.selectAll;

            if (this.selectAll) {
                this.newRoles = [...this.roles]
            } else {
                this.newRoles = []
            }
        }
    }
}
</script>

<style scoped>
    .checkmark {
        height: 20px;
        width: 20px;
        background-color: #eee;
        cursor: pointer;
    }
</style>