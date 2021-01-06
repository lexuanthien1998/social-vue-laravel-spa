<template>
    <v-app>
    <div class="container-fluid container-lg">
        <div class="vh-100 d-flex overflow-scroll p-3">
            <div v-if="loggedIn" class="d-none d-md-block w-25 mh-100 sticky-top page-main">
                <my-menu-pc></my-menu-pc>
            </div>
            <div v-if="loggedIn" class="d-none d-sm-block d-md-none mw-25 mh-100 sticky-top page-main-sm">
                <my-menu-tl></my-menu-tl>
            </div>
            <div v-if="loggedIn" class="d-block d-sm-none sticky-top position-fixed w-100 shadow-sm page-main-sp">
                <my-menu-sp></my-menu-sp>
            </div>
            <!-- <my-heading v-if="loggedIn"></my-heading> -->
            
            <div class="w-50 mx-auto align-self-center" v-if="$route.name == 'login'">
                <router-view></router-view>
            </div>
            <div class="w-50 mx-auto align-self-center" v-else-if="$route.name == 'register'">
                <router-view></router-view>
            </div>
            <div class="w-100 px-0 px-sm-5 px-md-10 page-main-PC" v-else>
                <router-view></router-view>
            </div>

            <my-sidebar v-if="loggedIn"></my-sidebar>
        </div>
    </div>
    </v-app>
</template>

<script>
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
            }
        },
        computed: {
            loggedIn() {
                return this.$store.getters.loggedIn
            },
            getUser() {
                return this.$store.getters.getUser
            }
        }
    }
</script>