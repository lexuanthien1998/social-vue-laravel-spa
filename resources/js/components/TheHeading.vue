<template>
    <div class="sticky-top">
        <div class="d-none d-md-block w-25 mh-100 page-main">
            <router-link :to="{name:'home'}">
                <div class="box-content rounded-pill shadow-sm" v-bind:class="[$route.name == 'home' ? 'active' : '']"><i class="fas fa-home rounded-circle"></i><span>Home</span></div>
            </router-link>
            <div class="box-content rounded-pill shadow-sm" v-on:click="openModal()"><i class="fas fa-feather-alt rounded-circle"></i><span>Post</span></div>
            <div class="box-content rounded-pill shadow-sm"><i class="fas fa-bell rounded-circle"></i><span>Notifications</span></div>
            <div class="box-content rounded-pill shadow-sm"><i class="fab fa-facebook-messenger rounded-circle"></i><span>Messenger</span></div>
            <router-link :to="{name: 'profile', params: { username: user.username } }">
                <div class="box-content rounded-pill shadow-sm" v-bind:class="[$route.name == 'profile' ? 'active' : '']"><i class="fas fa-user rounded-circle"></i><span>Profile</span></div>
            </router-link>
            <router-link :to="{name: 'logout'}">
                <div class="box-content rounded-pill shadow-sm sidebar-footer"><i class="fas fa-power-off rounded-circle"></i><span>Logout</span></div>
            </router-link>
        </div>

        <div class="d-none d-sm-block d-md-none mw-25 mh-100 d-sm-flex justify-content-start flex-column page-main-sm">
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
            <router-link :to="{name:'logout'}">
                <i class="fas fa-power-off rounded-circle shadow-sm sidebar-footer"></i>
            </router-link>
        </div>

        <div class="d-block d-sm-none sticky-top position-fixed w-100 mh-100 d-flex justify-content-between shadow-sm page-main-sp">
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
            <router-link :to="{name:'logout'}">
                <i class="fas fa-power-off rounded-circle shadow-sm"></i>
            </router-link>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
            }
        },
        methods: {
            openModal() {
                console.log(this.user)
                this.$router.push({ name: 'home', query: { action: 'create' } }).catch(()=>{});
            },
        }
    }
</script>
