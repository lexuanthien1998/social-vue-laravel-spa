<template>
    <div class="d-flex justify-content-between">
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
</template>

<script>
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
                active: 'active',
            }
        },
    }
</script>
