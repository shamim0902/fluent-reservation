import {createApp, nextTick} from 'vue'

import ElementPlus, {ElMessageBox, ElNotification, ElLoadingDirective} from 'element-plus';

import {createRouter, createWebHistory} from "vue-router";
import App from "./App.vue";


const routes = [];


const router = createRouter(
    {
        history: createWebHistory(''),
        routes: routes
    },
);


const app = createApp(App);
app
    .use(router)
    //.use(ElementPlus)
    .component('app', App);

app.mount('#room-list-app');
