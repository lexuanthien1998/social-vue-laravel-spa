<template>
    <div class="box-content p-5 shadow bg-white form-register">
        <form @submit.prevent="login">
            <h3 class="text-center">Login</h3>

            <div class="d-flex">
                <i class="fas fa-envelope-open"></i>
                <input type="email" placeholder="Email..." v-model="email" class="rounded-pill px-3 py-1 w-100">
            </div>

            <div class="d-flex">
                <i class="fas fa-key"></i>
                <input type="password" placeholder="Password..." v-model="password" class="rounded-pill px-3 py-1 w-100">
            </div>

            <div class="px-2" v-if="message != ''">
                <p class="text-message rounded-pill">{{message}}</p>
            </div>

            <div class="text-center">
                <button type="submit" class="btn rounded-pill">Login</button>
            </div>
            <div class="text-center box-next-page">
                <router-link :to="{name: 'register'}">or... Create new account</router-link>
            </div>      
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                email:'',
                password:'',
                message: '',
            }
        },
        methods: {
            login() {
                this.$store.dispatch('retrieveToken', {
                    email: this.email,
                    password: this.password,
                })
                .then(response => {
                    this.$router.push({ name: 'home' }).catch(()=>{});
                })
                .catch((error) => {
                    if (error.response) {
                        if(error.response.data.message) {
                            this.message = error.response.data.message
                        } else {
                            this.message = ''
                        }
                    } else {
                        console.log(error);
                    }
                });
            }
        }
    }
</script>