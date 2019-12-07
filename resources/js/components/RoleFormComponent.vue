<template>
    <div id="create-role-container" class="pb-4">
        <h1>Crea nuovo ruolo</h1>

        <form class="form-inline mt-4">
            <label class="sr-only" for="input-role-name">Name</label>
            <input v-model="roleName" type="text" :class="'form-control mb-2 mr-sm-2 col-12 col-lg-4 ' + (!roleState ? 'is-invalid' : '')" id="input-role-name" placeholder="Inserisci un nuovo ruolo">

            <button type="submit" class="btn btn-primary mb-2" @click.prevent="addRole" :disabled="!validate() || !roleState">Conferma</button>
        </form>
        <p v-if="roleName && roleState" class="text-muted">
            <span class="font-weight-bold">{{this.validate()}}</span> verrà aggiunto ai ruoli
        </p>
        <p v-if="!roleState">
            <span class="text-danger">Il ruolo non può superare i 20 caratteri.</span>
        </p>

        <h1 class="mt-5 mb-4">Lista ruoli</h1>
        <div v-if="loading" class="pr-3">
            <i class="fas fa-spinner fa-pulse"></i>Caricamento in corso...
        </div>
        <role-chip v-for="role in roles" :id="role.id" :name="role.name" :key="role.id" :onDelete="() => deleteRole(role.id)"></role-chip>
    </div>
</template>

<script>
    import Chip from './Chip';
    import RoleChip from './RoleChip';
    import { EventBus } from "../event-bus";

    export default {

        components: {
            chip: Chip,
            "role-chip": RoleChip 
        },

        created() {
            this.fetchRoles();
        },

        data() {
            return {
                roleName: null,
                errorMessage: null,
                roles: [],
                loading: false,
            }
        },
        computed: {
            roleState () {
                if(!this.roleName){
                    return true;
                }

                return this.roleName.trim().length <= 20;
            }
        },

        methods: {

            fetchRoles() {
                this.loading = true;
                let vm = this;

                axios.get('/admin/roles').then(data => {
                    vm.roles = data.data;
                    vm.loading = false;
                }).catch(error => {
                    EventBus.$emit('newNotification', {
                       type: 'ERROR',
                       message: 'Errore durante il caricamento dei ruoli'
                    });
                    vm.loading = false
                });
            },

            addRole() {
                let newRole = this.validate();

                if (!newRole) {
                    return;
                }

                let vm = this;
                axios.post('/admin/roles', {
                    name: newRole
                }).then(data => {
                    vm.roles.push(data.data);
                    
                    vm.roles.sort((a,b) => (a.name > b.name ? 1 : -1));
                    vm.roleName = null;
                }).catch(error => {
                    let message = error.response.status == 400 ? 'Ruolo già esistente' : 'Errore inaspettato';
                    EventBus.$emit('newNotification', {
                            type: 'ERROR',
                            message: message
                    });
                });
            },

            validate() {
                let role = this.roleName ? this.roleName.trim() : null;

                if (!role) {
                    return false;
                }

                return role.replace(/\s/g, '_').toUpperCase();
            },

            deleteRole(id) {
                let vm = this;

                return new Promise((resolve, reject) => {
                    axios.delete(`/admin/roles/${id}`).then((response) => {
                        vm.roles = vm.roles.filter((role) => {
                            return role.id !== id;
                        });
                        resolve();
                    }).catch((error) => {
                        EventBus.$emit('newNotification', {
                            message: 'Errore durante la cancellazione del ruolo',
                            type: 'ERROR'
                        });
                        reject();
                    });
                });
            }
        }
    }
</script>

<style scoped>

</style>