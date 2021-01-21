<template>
    <div class="box-content p-5 shadow text-center form-login">
        <form @submit.prevent="login">
            <h3 >Login</h3>
            <div class="d-flex align-items-center rounded-pill box-input">
                <i class="fas fa-envelope-open"></i>
                <input type="email" placeholder="your email." v-model="email">
            </div>
            <div class="d-flex align-items-center rounded-pill box-input">
                <i class="fas fa-key"></i>
                <input type="password" placeholder="your password." v-model="password">
            </div>
            <p v-if="message != ''" class="text-message rounded-pill">{{message}}</p>
            <button type="submit" class="w-100 btn rounded-pill">Login</button>
            <div>
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