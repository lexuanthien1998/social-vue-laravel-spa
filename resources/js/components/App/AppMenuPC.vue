<template>
    <div>
        <router-link :to="{name:'home'}">
            <div class="box-content rounded-pill shadow-sm" v-bind:class="[$route.name == 'home' ? 'active' : '']"><i class="fas fa-home rounded-circle"></i><span>Home</span></div>
        </router-link>
        <div class="box-content rounded-pill shadow-sm" v-on:click="openModal()"><i class="fas fa-feather-alt rounded-circle"></i><span>Post</span></div>
        <div class="box-content rounded-pill shadow-sm"><i class="fas fa-bell rounded-circle"></i><span>Notifications</span></div>
        <router-link :to="{name: 'messages'}">
            <div class="box-content rounded-pill shadow-sm"><i class="fab fa-facebook-messenger rounded-circle"></i><span>Messages</span></div>
        </router-link>
        <router-link :to="{name: 'profile', params: { username: getUser.username } }">
            <div class="box-content rounded-pill shadow-sm" v-bind:class="[$route.name == 'profile' ? 'active' : '']"><i class="fas fa-user rounded-circle"></i><span>Profile</span></div>
        </router-link>
        <router-link :to="{name: 'logout'}">
            <div class="box-content rounded-pill shadow-sm sidebar-footer"><i class="fas fa-power-off rounded-circle"></i><span>Logout</span></div>
        </router-link>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                user: this.$store.getters.getUser,
            }
        },
        computed: {
            getUser() {
                return this.$store.getters.getUser
            }
        },
        methods: {
            openModal() {
                this.$router.push({ name: 'home', query: { action: 'create' } }).catch(()=>{});
            },
        }
    }
</script>

<!--
<div v-if="loggedIn" class="d-none d-md-block w-25 mh-100 sticky-top page-main">
    <router-link :to="{name:'home'}">
        <div class="box-content rounded-pill shadow-sm" v-bind:class="[$route.name == 'home' ? active : '']"><i class="fas fa-home rounded-circle"></i><span>Home</span></div>
    </router-link>
    <div class="box-content rounded-pill shadow-sm" v-on:click="openModal()"><i class="fas fa-feather-alt rounded-circle"></i><span>Post</span></div>
    <div class="box-content rounded-pill shadow-sm"><i class="fas fa-bell rounded-circle"></i><span>Notifications</span></div>
    <div class="box-content rounded-pill shadow-sm"><i class="fab fa-facebook-messenger rounded-circle"></i><span>Messenger</span></div>
    <router-link :to="{name: 'profile', params: { username: user.username } }">
        <div class="box-content rounded-pill shadow-sm" v-bind:class="[$route.name == 'profile' ? active : '']"><i class="fas fa-user rounded-circle"></i><span>Profile</span></div>
    </router-link>
    <router-link :to="{name: 'login'}">
        <div v-if="!loggedIn" class="box-content rounded-pill shadow-sm box-login"><i class="fas fa-sign-in-alt rounded-circle"></i><span>Login</span></div>
    </router-link>
    <router-link :to="{name: 'register'}">
        <div v-if="!loggedIn" class="box-content rounded-pill shadow-sm sidebar-footer"><i class="fas fa-pen-alt rounded-circle"></i><span>Register</span></div>
    </router-link>
    <router-link :to="{name: 'logout'}">
        <div v-if="loggedIn" class="box-content rounded-pill shadow-sm sidebar-footer"><i class="fas fa-power-off rounded-circle"></i><span>Logout</span></div>
    </router-link>
</div>

<div v-if="loggedIn" class="d-none d-sm-block d-md-none mw-25 mh-100 sticky-top d-sm-flex justify-content-start flex-column page-main-sm">
    <router-link :to="{name:'home'}">
        <i class="fas fa-home rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'home'}">
        <i class="fas fa-feather-alt rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'home'}">
        <i class="fas fa-bell rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'home'}">
        <i class="fab fa-facebook-messenger rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'home'}">
        <i class="fas fa-user rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name: 'login'}">
        <i v-if="!loggedIn" class="fas fa-sign-in-alt rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name: 'register'}">
        <i v-if="!loggedIn" class="fas fa-pen-alt rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'logout'}">
        <i class="fas fa-power-off rounded-circle shadow-sm sidebar-footer"></i>
    </router-link>
</div>

<div v-if="loggedIn" class="d-block d-sm-none sticky-top position-fixed w-100 d-flex justify-content-between shadow-sm page-main-sp">
    <router-link :to="{name:'home'}">
        <i class="fas fa-home rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'home'}">
        <i class="fas fa-feather-alt rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'home'}">
        <i class="fas fa-bell rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'home'}">
        <i class="fab fa-facebook-messenger rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'home'}">
        <i class="fas fa-user rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name: 'login'}">
        <i v-if="!loggedIn" class="fas fa-sign-in-alt rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name: 'register'}">
        <i v-if="!loggedIn" class="fas fa-pen-alt rounded-circle shadow-sm"></i>
    </router-link>
    <router-link :to="{name:'logout'}">
        <i class="fas fa-power-off rounded-circle shadow-sm"></i>
    </router-link>
</div>
-->
