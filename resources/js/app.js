require('./bootstrap');
window.Vue = require('vue');

//axios
import axios from 'axios';
import VueAxios from 'vue-axios';
Vue.use(VueAxios, axios);

// vuex
import store from "./store";

//Component App là file Master FE
import App from './components/App';

//Khai báo các Components
Vue.component('example-component', require('./components/ExampleComponent.vue').default);

// axios.defaults.withCredentials = true;
// axios.defaults.baseURL = 'https://gabbyblog.herokuapp.com/';

//vuetify
import vuetify from '../plugins/vuetify';

//router.js
import router from './router';

const app = new Vue({
    el: '#app',
    store: store,
    router: router,
    vuetify: vuetify,
    render: h => h(App)
});

// - Install project Laravel
// - Install laravel/ui (laravel 7.0 thì chọn laravel/ui 2.0)
// - Run php artisan ui vue
// - Run npm install
// - Run npm run dev (lệnh này tạo ra 2 forder css and js trong /public của Laravel)