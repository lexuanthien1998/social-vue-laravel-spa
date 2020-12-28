import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

// vuex
import store from "./store";

// File Views
import LoginUser from './views/user/LoginUser.vue';
import RegisterUser from './views/user/RegisterUser.vue';
import LogoutUser from './views/user/LogoutUser.vue';
import Profile from './views/user/Profile.vue';

import profilePosts from './views/user/profile/profilePosts.vue';
import profilePhotos from './views/user/profile/profilePhotos.vue';
import profileIntro from './views/user/profile/profileIntro.vue';
import profileFollowers from './views/user/profile/profileFollowers.vue';
import profileFollowing from './views/user/profile/profileFollowing.vue';


import HomePage from './views/page/Home.vue';
import PostDetails from './views/page/Details.vue';


const routes = [
    { path: '/', name: 'home', component: HomePage, meta: { requiresAuth: true } },
    { path: '/:id/details', name: 'post-details', component: PostDetails, meta: { title: 'Details', requiresAuth: true } },

    { path: '/user/:username', name: 'profile', component: Profile, meta: { title: 'Profile page', requiresAuth: true },
        children: [
            { path: 'posts', name: 'posts', component: profilePosts, meta: { title: 'posts' }},
            { path: 'photos', name: 'photos', component: profilePhotos, meta: { title: 'photos' }},
            { path: 'intro', name: 'intro', component: profileIntro, meta: { title: 'intro' }},
            { path: 'followers', name: 'followers', component: profileFollowers, meta: { title: 'followers' }},
            { path: 'following', name: 'following', component: profileFollowing, meta: { title: 'following' }}
        ]
    },
    { path: '/login', name: 'login', component: LoginUser, meta: { title: 'Login page', requiresVisitor: true } },
    { path: '/register', name: 'register', component: RegisterUser, meta: { title: 'Register page', requiresVisitor: true } },
    { path: '/logout', name: 'logout', component: LogoutUser},
    { path: '/:pathMatch(.*)*', redirect: { name: 'home' } }
    //requiresAuth : Nếu chưa Login thì không được truy cập
    //requiresVisitor : Nếu đã Login thì không được truy cập
];

const router = new VueRouter({
    mode:'history',
    routes
});

//READ TITLE
router.beforeEach((to, from, next) => {
    let title = to.meta.title || 'ʟ ᴏ ɴ ᴇ ʟ ʏ';
    document.title = title;
    next();

    if (to.meta.requiresAuth) {
        if (!store.getters.loggedIn) {
            next({
                name: 'login',
            })
        } else {
            next()
        }
    } else if (to.meta.requiresVisitor) {
        if (store.getters.loggedIn) {
            next({
                name: 'home',
            })
        } else {
            next()
        }
    } else {
        next()
    }
});

export default router;