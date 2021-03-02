<template>
    <div class="d-none d-lg-block sticky-top box-main-right">
        <div class="rounded-pill shadow-sm px-4 w-100 d-flex align-items-center position-relative search-box" v-if="$route.name != 'music'">
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
        <div v-if="users.length >= 1 && $route.name != 'music'" class="shadow-sm my-4 box-content box-followes">
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
        <div class="shadow-sm box-content tracks" v-if="tracks != '' " v-bind:style="{height: $route.name == 'music' ? '100%' : 'auto'}">
            <div class="d-flex p-2">
                <img :src="tracks.thumbnail" class="img-fluid" alt="">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-self-center">
                        <p class="m-0">{{tracks.name}}</p>
                        <i class="fas fa-heart"></i>
                    </div>
                    <p class="m-0">{{tracks.artists_names}}</p>
                </div>
            </div>

            <div class="tracks-action text-center p-2">
                <input type="range" ref="timeline" value="0">
                <div class="d-flex align-items-center justify-content-evenly">
                    <i class="fas fa-fast-backward"></i>
                    <i @click="play()" class="fas fa-play icon-play"></i>
                    <i @click="pause()" class="fas fa-pause icon-play" style="display: none;"></i>
                    <i class="fas fa-fast-forward"></i>
                </div>
            </div>
            
            <audio ref="tracks" controls hidden>
                <source :src="'http://vnso-zn-15-tf-mp3-320s1-zmp3.zadn.vn/b388618ac9cd209379dc/1596613103323736812?authen=exp=1614823069~acl=/b388618ac9cd209379dc/*~hmac=cf6abf13a5921eed1748b43a6eaf5f96&fs=MTYxNDY1MTIwMjQ5OXx3ZWJWNQ'" type="audio/ogg">
                <source :src="'http://vnso-zn-15-tf-mp3-320s1-zmp3.zadn.vn/b388618ac9cd209379dc/1596613103323736812?authen=exp=1614823069~acl=/b388618ac9cd209379dc/*~hmac=cf6abf13a5921eed1748b43a6eaf5f96&fs=MTYxNDY1MTIwMjQ5OXx3ZWJWNQ'" type="audio/mpeg">
            </audio>
        </div>
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
                data: [],

                songs: this.$store.getters.getSongs,
                tracks: this.$store.getters.getSongs[0]
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

            // music
            this.$refs.tracks.onloadedmetadata = () => this.$refs.timeline.max = this.$refs.tracks.duration
            this.$refs.tracks.ontimeupdate = () => this.$refs.timeline.value = this.$refs.tracks.currentTime
            this.$refs.timeline.onchange = () => this.$refs.tracks.currentTime = this.$refs.timeline.value
            
            var self = this;
            self.$refs.tracks.addEventListener('ended',function(e){
                var isPlaylist = self.songs.find(item => item.id == self.tracks.id);
                if(typeof isPlaylist == 'undefined') {
                    return
                }
                var rank = Object.keys(self.songs).find(key => self.songs[key].id === self.tracks.id);
                self.tracks = self.songs.slice(parseInt(rank) + 1, parseInt(rank) + 2)[0];
                
                self.$refs.tracks.onloadedmetadata = () => self.$refs.timeline.max = self.$refs.tracks.duration
                self.$refs.tracks.ontimeupdate = () => self.$refs.timeline.value = self.$refs.tracks.currentTime
                self.$refs.timeline.onchange = () => self.$refs.tracks.currentTime = self.$refs.timeline.value

                self.$refs.tracks.pause();
                self.$refs.tracks.load();
                self.$refs.tracks.oncanplaythrough = self.$refs.tracks.play();
            });
        },
        watch: {
            keyword: function (newKeyword, oldKeyword) {
                this.loading = true;
                this.debouncedGetAnswer();
            },
            // music
            '$store.state.tracks': function() {
                if(this.$store.state.tracks != this.tracks.id) {
                    var tracks = this.songs.filter(item => item.id == this.$store.state.tracks);
                    if(tracks.length > 0) {
                        this.tracks = tracks[0]
                    }
                    this.$refs.tracks.onloadedmetadata = () => this.$refs.timeline.max = this.$refs.tracks.duration
                    this.$refs.tracks.ontimeupdate = () => this.$refs.timeline.value = this.$refs.tracks.currentTime
                    this.$refs.timeline.onchange = () => this.$refs.tracks.currentTime = this.$refs.timeline.value

                    this.$refs.tracks.pause();
                    this.$refs.tracks.load();
                    this.$refs.tracks.oncanplaythrough = this.$refs.tracks.play();
                }
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
            },
            // music
            play() {
                if (this.$refs.tracks.paused) {
                    this.$refs.tracks.play();
                } else {
                    this.$refs.tracks.pause();
                }
            }
        }
    }
</script>
