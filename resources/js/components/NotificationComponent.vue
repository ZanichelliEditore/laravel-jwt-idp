<template>
    <div v-if="currentNotification" id="notification-container" :class="getBackgroundByType(currentNotification.type) + ' d-flex flex-column position-fixed ml-auto mr-auto col-10 col-md-4 col-lg-2 px-3 py-3 text-white align-items-center'">
        <span>{{currentNotification.message}}</span>
        <i v-if="currentNotification.type === 'SUCCESS'" class="fa fa-check fa-2x mt-2"></i>
        <i v-if="currentNotification.type === 'ERROR'" class="fa fa-times fa-2x mt-2"></i>
        <i v-if="currentNotification.type === 'INFO'" class="fa fa-info-circle fa-2x mt-2"></i>
    </div>
</template>

<script>
    import { EventBus } from "../event-bus";

    export default {

        data() {
            return {
                processing: false,
                notifications: [],
                currentNotification: null,
                types: ['SUCCESS', 'ERROR', 'INFO']
            }
        },

        created() {
            let vm = this;
            EventBus.$on('newNotification', notification => {
                if (notification.hasOwnProperty('message') && notification.hasOwnProperty('type')) {
                    vm.notifications.push(notification);
                }
            });
        },

        methods: {

            processNotification() {

                let vm = this;

                if (vm.notifications.length && !vm.processing) {
                    vm.processing = true;
                    vm.currentNotification = vm.notifications[0];

                    return setTimeout(function () {
                        vm.processing = false;
                        vm.currentNotification = null;

                        setTimeout(function () {
                            vm.notifications.shift();
                        }, 200);

                    }, 3000);
                }

                vm.processing = false;
            },

            getBackgroundByType(type) {
                switch (type) {
                    case 'SUCCESS':
                        return 'bg-success';
                    case 'ERROR':
                        return 'bg-danger';
                    case 'INFO':
                        return 'bg-info';
                    default:
                        return 'bg-success';
                }
            }

        },

        watch: {
            notifications() {
                this.processNotification();
            }
        }
    }
</script>

<style scoped>
    #notification-container {
        left: 0;
        right: 0;
        z-index: 100;
        border-radius: .35rem;
        animation-name: fade-notification;
        animation-duration: 3s;
        animation-timing-function: linear;
        opacity: 0;
    }

    @keyframes fade-notification {
        0%, 100% {
            top: 0;
            opacity: 0;
        }
        10% {
            top: 20px;
            opacity: 1;
        }
        90% {
            top: 20px;
            opacity: 1;
        }
    }

</style>