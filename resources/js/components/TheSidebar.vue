<template>
    <div class="d-none d-lg-block sticky-top box-main-right">
        <div class="rounded-pill shadow-sm px-4 w-100 d-flex align-items-center position-relative search-box">
            <div class="d-flex align-items-center dropdown w-100">
                <input type="text" placeholder="your keywords." v-model="keyword" class="w-100" data-toggle="dropdown">
                <div class="item loading-3" v-if="loading">
                    <div class="loading"></div>
                </div>
                <i class="fas fa-times" v-else-if="isKeywords" v-on:click="deleteKeywords()"></i>
                <button type="button" v-else><i class="fa fa-search" aria-hidden="true"></i></button>

                <div class="dropdown-menu w-100 shadow px-3 overflow-scroll">
                    <div class="d-flex align-items-center p-2" v-if="data == '' && answer != null">
                        <p>{{answer}}</p>
                    </div>
                    <router-link :to="{name: 'profile', params: { username: item.username } }" class="rounded-pill d-flex align-items-center p-2 m-1" v-else v-for="(item, index) in data" :key='index'>
                        <img :src="item.image_profile != null ? `../storage/images/users/`+ item.id + `/image_profile/` + item.image_profile : `/images/user.png`" class="img-fluid">
                        <p>{{ item.username }}</p>
                    </router-link>
                </div>
            </div>
        </div>
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
        <!-- <div class="d-flex sidebar-footer box-info-design">
            <i class="fas fa-heart"></i>
        </div> -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                user: this.$store.getters.getUser,
                users: [],
                keyword: '',
                loading: false,
                isKeywords: false,
                answer: null,
                data: []
            }
        },
        mounted() {
            this.axios
            .get('/api/users', {
                params: { id: this.user.id }
            })
            .then(response => {
                const items = response.data.filter(user => user.id != this.user.id);
                this.users = items;
            })
            .catch(response => {
                return
            });
        },
        watch: {
            keyword: function (newKeyword, oldKeyword) {
                this.loading = true;
                this.debouncedGetAnswer();
            }
        },
        created: function () {
            this.debouncedGetAnswer = _.debounce(this.getAnswer, 500);
        },
        methods: {
            getAnswer: function () {
                if (this.keyword == '') {
                    this.loading = false;
                    this.isKeywords = false;
                    this.data = [];
                    this.answer = null;
                    return
                }
                var vm = this;
                axios
                .get('/api/search', {
                    params: {
                        keyword: this.keyword
                    }
                })
                .then(function (response) {
                    vm.loading = false;
                    vm.isKeywords = true;
                    vm.data = response.data;
                    if(vm.data.length == 0) {
                        vm.answer = 'No results found.';
                    }
                })
                .catch(function (error) {
                    return
                })
            },
            deleteKeywords() {
                this.keyword = '';
            },
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
