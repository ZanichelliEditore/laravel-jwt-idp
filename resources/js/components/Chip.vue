<template>
    <div class="d-inline-flex align-items-center chip mb-3 mr-2">
        <div class="d-flex justify-content-center align-items-center circle">
            {{ id }}
        </div>
        <div class="d-flex justify-content-center align-items-center body pl-3 pr-3">
            {{ name }}
        </div>
        <div v-if="!requestDelete" class="d-flex justify-content-center align-items-center pl-1 pr-3 icon-close">
            <div>
                <button class="btn-delete" @click="deleteChip"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div v-if="requestDelete" class="d-flex align-items-center pl-1 pr-1">
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
    export default {
        name: "Chip",

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
                deleting: false
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

            deleteChip() {
                this.requestDelete = true;
            },

            cancelDelete() {
                this.requestDelete = false;
            },

            confirmDelete() {
                this.deleting = true;

                let vm = this;

                this.onDelete().then(() => {
                    vm.deleting = false;
                }).catch(() => {
                    vm.deleting = false;
                });
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
</style>