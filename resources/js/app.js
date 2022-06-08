import './bootstrap';
import Vue from "vue"
import VModal from 'vue-js-modal'
Vue.use(VModal)
import App from './components/App.vue'
import Notifications from 'vue-notification'
Vue.use(Notifications)
import VueElementLoading from "vue-element-loading";
Vue.component("VueElementLoading", VueElementLoading);

const app = new Vue({
    el: '#app',
    components: {App}
})