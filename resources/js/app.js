require('./bootstrap');

window.Vue = require('vue');

import App from './components/layouts/App';
import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routers/routers';
import store from './stores/store';

Vue.use(VueRouter);

const router = new VueRouter({
    routes,
    mode: 'history',
});
window.events = new Vue();

const app = new Vue({
    el: '#app',
    template: '<App/>',
    render: h => h(App),
    router,
    store: store,
});
