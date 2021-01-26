<template>
    <v-app>
    <div class="container-fluid container-lg">
        <div class="vh-100 d-flex overflow-scroll p-3">
            <my-heading v-if="loggedIn"></my-heading>
            <div class="mx-auto align-self-center" v-if="$route.name == 'login' || $route.name == 'register'">
                <router-view></router-view>
            </div>
            <div class="w-100 px-0 px-sm-5 px-md-10 page-main-PC" v-else>
                <router-view></router-view>
            </div>
            <my-sidebar v-if="loggedIn && $route.name != 'messages'"></my-sidebar>
        </div>
    </div>
    <notifications class="notifications" group="foo" />
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