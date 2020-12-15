require('./bootstrap');
window.Vue = require('vue');

//axios
import axios from 'axios';
import VueAxios from 'vue-axios';
Vue.use(VueAxios, axios);

// vuex
import store from "./store";

//router.js
import router from './router';

//Component App là file Master FE
import App from './components/App';

//Khai báo các Components
Vue.component('example-component', require('./components/ExampleComponent.vue').default);

axios.defaults.withCredentials = true
// axios.defaults.baseURL = 'https://gabbyblog.herokuapp.com/';

const app = new Vue({
    el: '#app',
    store: store,
    router: router,
    render: h => h(App)
});

// - Install project Laravel
// - Install laravel/ui (laravel 7.0 thì chọn laravel/ui 2.0)
// - Run php artisan ui vue
// - Run npm install
// - Run npm run dev (lệnh này tạo ra 2 forder css and js trong /public của Laravel)