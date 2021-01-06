<template>
    <div class="d-none d-lg-block sticky-top box-main-right">
        <form @submit.prevent="Search" class="rounded-pill shadow-sm px-4 w-100 d-flex align-items-center search-box">
            <input type="text" placeholder="Search..." class="w-100">
            <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
        </form>
        <div v-if="users.length > 1" class="shadow-sm my-4 box-content box-followes">
            <p class="text px-2 pt-2">maybe you...know</p>
            <div v-for="(member, index) in users" :key="index" class="px-2 pb-2 d-flex justify-content-between align-items-center">
                <div class="d-flex">
                    <img src="/images/avatar.jpg" class="img-fluid">
                    <a href="">
                        <p class="username">{{member.username}}</p>
                    </a>
                </div>
                <p class="btn-follow" v-on:click="follow(index, $event)">follow</p>
            </div>
        </div>
        <div class="box-info-design">
            <p>design by <i class="fas fa-heart"></i> lexuanthien</p>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                user: this.$store.getters.getUser,
                users: [],
            }
        },
        mounted() {
            this.axios
            .get('/api/users')
            .then(response => {
                const items = response.data.filter(user => user.id != this.user.id);
                this.users = items
            })
            .catch(response => {
                return
            });
        },
        methods: {
            follow(index, e) {
                if(e.target.innerHTML == 'follow') {
                    axios.post('/api/follow', {
                        user_id: this.user.id,
                        id: this.users[index].id,
                    })
                    .then((response) => {
                        e.target.innerHTML = 'unfollow';
                    })
                    .catch((error) => {
                        return
                    });
                } else {
                    axios.post('/api/unfollow', {
                        user_id: this.user.id,
                        id: this.users[index].id,
                    })
                    .then((response) => {
                        e.target.innerHTML = 'follow';
                    })
                    .catch((error) => {
                        return
                    });
                }
            }
        }
    }
</script>
