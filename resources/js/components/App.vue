<template>
    <v-app>
    <div class="container-fluid container-lg">
        <div class="vh-100 d-flex overflow-scroll p-3">
            <div v-if="loggedIn" class="d-none d-md-block w-25 mh-100 sticky-top page-main">
                <router-link :to="{name:'home'}">
                    <div class="box-content rounded-pill shadow-sm" v-bind:class="[$route.name == 'home' ? active : '']"><i class="fas fa-home rounded-circle"></i><span>Home</span></div>
                </router-link>
                <div class="box-content rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createPost"><i class="fas fa-feather-alt rounded-circle"></i><span>Post</span></div>
                <div class="box-content rounded-pill shadow-sm"><i class="fas fa-bell rounded-circle"></i><span>Notifications</span></div>
                <div class="box-content rounded-pill shadow-sm"><i class="fab fa-facebook-messenger rounded-circle"></i><span>Messenger</span></div>
                <router-link :to="{name: 'profile', params: { username: user.username } }">
                    <div class="box-content rounded-pill shadow-sm" v-bind:class="[$route.name == 'profile' ? active : '']"><i class="fas fa-user rounded-circle"></i><span>Profile</span></div>
                </router-link>
                <router-link :to="{name: 'login'}">
                    <div v-if="!loggedIn" class="box-content rounded-pill shadow-sm box-login"><i class="fas fa-sign-in-alt rounded-circle"></i><span>Login</span></div>
                </router-link>
                <router-link :to="{name: 'register'}">
                    <div v-if="!loggedIn" class="box-content rounded-pill shadow-sm sidebar-footer"><i class="fas fa-pen-alt rounded-circle"></i><span>Register</span></div>
                </router-link>
                <router-link :to="{name: 'logout'}">
                    <div v-if="loggedIn" class="box-content rounded-pill shadow-sm sidebar-footer"><i class="fas fa-power-off rounded-circle"></i><span>Logout</span></div>
                </router-link>
            </div>

            <div v-if="loggedIn" class="d-none d-sm-block d-md-none mw-25 mh-100 sticky-top d-sm-flex justify-content-start flex-column page-main-sm">
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
                <router-link :to="{name: 'login'}">
                    <i v-if="!loggedIn" class="fas fa-sign-in-alt rounded-circle shadow-sm"></i>
                </router-link>
                <router-link :to="{name: 'register'}">
                    <i v-if="!loggedIn" class="fas fa-pen-alt rounded-circle shadow-sm"></i>
                </router-link>
                <router-link :to="{name:'logout'}">
                    <i class="fas fa-power-off rounded-circle shadow-sm sidebar-footer"></i>
                </router-link>
            </div>

            <div v-if="loggedIn" class="d-block d-sm-none sticky-top position-fixed w-100 d-flex justify-content-between shadow-sm page-main-sp">
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
                <!-- <router-link :to="{name: 'login'}">
                    <i v-if="!loggedIn" class="fas fa-sign-in-alt rounded-circle shadow-sm"></i>
                </router-link>
                <router-link :to="{name: 'register'}">
                    <i v-if="!loggedIn" class="fas fa-pen-alt rounded-circle shadow-sm"></i>
                </router-link> -->
                <router-link :to="{name:'logout'}">
                    <i class="fas fa-power-off rounded-circle shadow-sm"></i>
                </router-link>
            </div>

            <div class="w-50 mx-auto align-self-center" v-if="$route.name == 'login'">
                <router-view></router-view>
            </div>
            <div class="w-50 mx-auto align-self-center" v-else-if="$route.name == 'register'">
                <router-view></router-view>
            </div>
            <div class="w-100 px-0 px-sm-5 px-md-10 page-main-PC" v-else>
                <router-view></router-view>
            </div>

            <div v-if="loggedIn" class="d-none d-lg-block sticky-top box-main-right">
                <form @submit.prevent="Search" class="w-100 d-flex">
                    <input type="text" placeholder="Search..." class="rounded-pill shadow-sm px-4 py-2 w-100">
                    <button type="submit" class="btn btn-sm rounded-pill" hidden></button>
                </form>
                <div class="shadow-sm my-4 box-content box-followes">
                    <p class="text-center">maybe you...know</p>
                    <div v-for="(member, index) in users" :key="index" class="p-2 d-flex justify-content-between align-items-center">
                        <div class="d-flex">
                            <img src="/images/avatar.jpg" class="img-fluid">
                            <a href="">
                                <p class="username">{{member.username}}</p>
                            </a>
                        </div>
                        <p class="btn-follow">follow</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="createPost" ref="hiddenmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content px-3">
                    <div class="modal-header pb-0">
                        <h5 class="modal-title"><i class="fas fa-feather-alt mr-1" style="color:#ff7e67;"></i>create...</h5>
                        <i class="fas fa-times" data-bs-dismiss="modal"></i>
                    </div>
                    <form @submit.prevent="submitPostModal" class="form-post p-3" enctype="multipart/form-data">
                        <div class="modal-body">
                            <textarea class="form-control" rows="3" v-model="content"></textarea>
                            <figure>
                                <img class="img-fluid mx-auto" id="img-modal" alt="">
                            </figure>
                        </div>
                        <div class="modal-footer px-0 d-flex justify-content-between">
                            <div class="image-upload">
                                <label for="file-input-modal"><i class="far fa-images"></i></label>
                                <input id="file-input-modal" v-on:change="onImageChangeModal" type="file"/>
                            </div>
                            <button type="submit" class="btn rounded-pill py-2 px-4">Đăng bài</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </v-app>
</template>

<script>
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
                active: 'active',
                users: [],
                content: '',
                image: '',
            }
        },
        mounted() {
            this.axios
            .get('/api/users')
            .then(response => {
                this.users = response.data
                console.log(response)
            })
            .catch(response => {
            });
            
            $(this.$refs.hiddenmodal).on("hidden.bs.modal", this.closeModal)
        },
        computed: {
            loggedIn() {
                return this.$store.getters.loggedIn
            },
            getUser() {
                return this.$store.getters.getUser
            }
        },
        methods: {
            onImageChangeModal(e){
                if (e.target.files[0]) {
                    if (e.target.files[0].type.slice(0, 6) === 'image/') {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            $('#img-modal').attr('src', e.target.result);
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    }
                }
            },
            submitPostModal(e) {
                e.preventDefault();
                this.image = $('#img-modal').attr('src');
                if(this.image != '' || this.content != '') {
                    if(!this.$store.getters.loggedIn) {
                        this.$router.push({ name: 'login' })
                    }
                    axios.post('/api/post/store', {
                        user_id: this.user.id,
                        content: this.content,
                        image: this.image,
                    })
                    .then((response) => {
                        this.image = '';
                        this.content = '';
                        $('#img-modal').attr('src', '');
                        $('.modal').modal('toggle');
                    })
                    .catch((error) => {
                        $('.modal').modal('toggle');
                        return
                    });
                }
            },
            closeModal() {
                this.image = '';
                this.content = '';
                $('#img-modal').attr('src', '');
            }
        },
    }
</script>