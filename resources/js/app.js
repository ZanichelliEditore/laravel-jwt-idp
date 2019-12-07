
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('users', require('./components/UsersComponent.vue'));

Vue.component('notification', require('./components/NotificationComponent'));

Vue.component('provider-form', require('./components/ProviderFormComponent'));

Vue.component('login-form', require('./components/LoginForm'));

Vue.component('manage-roles-panel', require('./components/RoleFormComponent'));

Vue.component('paginator', require('./components/Paginator'));

Vue.component('complete-registration-form', require('./components/CompleteRegistrationForm'));

Vue.component('oauth-clients', require('./components/OauthClientsTable'));

Vue.component('newclient-form', require('./components/NewClientFormComponent'));

const app = new Vue({
    el: '#app'
});
