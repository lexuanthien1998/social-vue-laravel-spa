import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate"; //Lưu thông tin lại, vuex chỉ lưu thông tin tạm thời, F5 sẽ bị mất
import axios from 'axios';
Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        token: localStorage.getItem('access_token') || null,
        user: {},
    },
    getters: {
        loggedIn(state) {
            return state.token !== null
        },
        getUser(state) {
            return state.user
        },
    },
    mutations: {
        retrieveToken(state, token) {
            state.token = token
        },
        addUser(state, user) {
            state.user = user
        },
        destroyToken(state) {
            state.token = null
            state.user = {}
        },
    },
    actions: {
        //Register
        register(context, data) {
            return new Promise((resolve, reject) => {
                axios
                .post('/api/register', {
                    name: data.name,
                    email: data.email,
                    password: data.password,
                })
                .then(response => {
                    resolve(response)
                })
                .catch(error => {
                    reject(error)
                })
            })
        },
        //Login
        retrieveToken(context, credentials) {
            return new Promise((resolve, reject) => {
                axios
                .post('/api/login', {
                    email: credentials.email,
                    password: credentials.password,
                })
                .then(response => {
                    const token = response.data.access_token
                    localStorage.setItem('access_token', token)
                    context.commit('retrieveToken', token)
                    context.commit('addUser', response.data.user)
                    resolve(response) //trả về response
                })
                .catch(error => {
                    reject(error) //trả về error
                })
            })
        },
        //Delete token (Logout)
        destroyToken(context) {
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + context.state.token
      
            if (context.getters.loggedIn) {
                return new Promise((resolve, reject) => {
                    axios.post('/api/logout')
                    .then(response => {
                        localStorage.removeItem('access_token')
                        context.commit('destroyToken')
                        resolve(response)
                    })
                    .catch(error => {
                        localStorage.removeItem('access_token')
                        context.commit('destroyToken')
                        reject(error)
                    })
                })
            }
        },
    },
    plugins: [createPersistedState()],
});

export default store;