<template>
    <div class="d-inline-flex align-items-center chip mb-3 mr-2">
        <div class="d-flex justify-content-center align-items-center circle">
            {{ id }}
        </div>
        <div class="d-flex justify-content-center align-items-center body pl-3 pr-3">
            {{ name }}
        </div>
        <div v-if="!requestDelete && !loading" class="d-flex justify-content-center align-items-center pl-1 pr-3 icon-close">
            <div>
                <button class="btn-delete" @click="deleteChipRole"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div v-if="loading" class="spinner-border spinner-border-sm mr-3" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <div v-if="requestDelete" class="d-flex align-items-center pl-1 pr-1">
            <div v-if="userRoles.length" class="arrow_box position-absolute">
                <span>N. associazioni presenti: {{userRoles.length}}</span>
            </div>
            <button class="btn btn-sm btn-danger confirm-button" @click="confirmDelete">
                <div v-if="deleting" class="spinner-border spinner-border-sm text-light" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <small v-else>Conferma</small>
            </button>
            <button class="btn btn-sm btn-dark cancel-button" @click="cancelDelete"><small>Annulla</small></button>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../event-bus';

    export default {
        name: "RoleChip",

        props: {
            id: {
                required: true
            },
            name: {
                required: true,
                type: String
            },
            onDelete: {
                required: true,
                type: Function
            }
        },

        data() {
            return {
                mouseHoverId: false,
                mouseHoverClose: false,
                requestDelete: false,
                deleting: false,
                userRoles: [],
                loading: false
            }
        },

        methods: {
            onMouseHoverId() {
                this.mouseHoverId = true;
                this.mouseHoverClose = true;
            },

            onMouseLeave() {
                this.mouseHoverClose = false;
                this.mouseHoverId = false;
            },

            deleteChipRole() {
                let vm = this;
                this.loading = true;

                axios.get(`/v1/roles/${vm.id}/user-roles`).then(response => {
                    vm.userRoles = response.data.data;
                    vm.loading = false;
                    vm.requestDelete = true;
                }).catch(error => {
                    EventBus.$emit('newNotification', {
                        message: 'Impossibile cancellare il ruolo selezionato',
                        type: 'ERROR'
                    });
                    vm.loading = false;
                })

            },

            cancelDelete() {
                this.requestDelete = false;
            },

            confirmDelete() {

                let vm = this;

                this.deleting = true;
                this.loading = false;

                this.onDelete().then(() => {
                    vm.deleting = false;
                }).catch(() => {
                    vm.deleting = false;
                })
            }
        }
    }
</script>

<style scoped>
    .chip {
        display: inline-block;
        height: 35px;
        background-color: #e5e5e5;
        min-width: 100px;
        border-radius: 20px;
        position: relative;
    }

    .chip .circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: #212121;
        color: white;
        font-weight: bold;
    }

    .icon-close:hover {
        color: red;
        cursor: pointer;
    }

    .btn-delete {
        background-color: transparent;
        border: none;
        outline: none;
    }

    .btn-delete:hover {
        color: red;
    }

    .confirm-button {
        border-radius: 20px 0 0 20px;
    }

    .cancel-button {
        border-radius: 0 20px 20px 0;
    }

    .arrow_box {
        background-color: #ffcdd2;
        color: #212121;
        font-weight: bold;
        width: 200px;
        top: -35px;
        padding: 3px 5px;
        border-radius: 3px;
        border: 1px solid red;
        display: flex;
    }

    .arrow_box:after, .arrow_box:before {
        top: 100%;
        left: 30px;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }

    .arrow_box:after {
        border-color: rgba(136, 183, 213, 0);
        border-top-color: #ffcdd2;
        border-width: 10px;
        margin-left: -10px;
    }
    .arrow_box:before {
        border-color: rgba(194, 225, 245, 0);
        border-top-color: red;
        border-width: 11px;
        margin-left: -11px;
    }
</style>