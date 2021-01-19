<template>
    <div class="d-none d-lg-block sticky-top box-main-right">
        <form @submit.prevent="Search" class="rounded-pill shadow-sm px-4 w-100 d-flex align-items-center search-box">
            <input type="text" placeholder="your keywords." class="w-100">
            <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
        </form>
        <div v-if="users.length >= 1" class="shadow-sm my-4 box-content box-followes">
            <p class="text px-2">maybe you...know !</p>
            <div v-for="(member, index) in users" :key="index" class="p-2 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img :src="member.image_profile != null ? `../storage/images/users/`+ member.id + `/image_profile/` + member.image_profile : `/images/user.png`" class="img-fluid">
                    <router-link :to="{name: 'profile', params: { username: member.username } }">
                        <p class="username">{{member.username}}</p>
                    </router-link>
                </div>
                <p class="btn-follow" v-on:click="follow(index, $event)">follow</p>
            </div>
        </div>
        <div class="box-info-design">
            <p><i class="fas fa-heart"></i></p>
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
            .get('/api/users', {
                params: { id: this.user.id }
            })
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
