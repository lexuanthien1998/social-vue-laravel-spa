<template>
        <!-- <nav class="container-fluid navbar navbar-expand-lg fixed-top navbar-light bg-white">
            <div class="container px-4">
                <router-link :to="{name: 'home'}" class="navbar-brand" >ʟ ᴏ ɴ ᴇ ʟ ʏ</router-link>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="#">Features</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                    </li>

                    <li class="nav-item active" v-if="!loggedIn">
                        <router-link :to="{name: 'login'}" class="nav-item nav-link" >Login</router-link>
                    </li>
                    <li class="nav-item" v-if="!loggedIn">
                        <router-link :to="{name: 'register'}" class="nav-item nav-link" >Register</router-link>
                    </li>
                    <li class="nav-item" v-if="loggedIn">
                        <router-link :to="{name: 'logout'}" class="nav-item nav-link" >Logout {{getUser.name}}</router-link>
                    </li>
                </ul>
                </div>
            </div>
        </nav> -->

        <div class="container">
            <div class="vh-100 d-flex overflow-scroll py-3">
                <div v-if="loggedIn" class="d-none d-xl-block w-25 mh-100 sticky-top page-main">
                    <router-link :to="{name:'home'}">
                        <div class="box-content rounded-pill shadow-sm"><i class="fas fa-home rounded-circle"></i><span>Home</span></div>
                    </router-link>
                    <div class="box-content rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createPost"><i class="fas fa-feather-alt rounded-circle"></i><span>Post</span></div>
                    <div class="box-content rounded-pill shadow-sm"><i class="fas fa-bell rounded-circle"></i><span>Notifications</span></div>
                    <div class="box-content rounded-pill shadow-sm"><i class="fab fa-facebook-messenger rounded-circle"></i><span>Messenger</span></div>
                    <router-link :to="{name: 'profile', params: { username: user.username } }">
                        <div class="box-content rounded-pill shadow-sm"><i class="fas fa-user rounded-circle"></i><span>Profile</span></div>
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

                <div v-if="loggedIn" class="d-block d-xl-none mw-25 mh-100 sticky-top position-fixed py-3 d-flex justify-content-start flex-column page-main-sp">
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
                    <router-link :to="{name: 'logout'}">
                        <i v-if="loggedIn" class="fas fa-power-off rounded-circle shadow-sm"></i>
                    </router-link>
                </div>

                <div class="w-50 mx-auto align-self-center" v-if="$route.name == 'login'">
                    <router-view></router-view>
                </div>
                <div class="w-50 mx-auto align-self-center" v-else-if="$route.name == 'register'">
                    <router-view></router-view>
                </div>
                <div class="w-100 d-inline-block mx-3 mx-xl-0 px-5 px-xl-4" v-else>
                    <router-view></router-view>
                </div>

                <div v-if="loggedIn" class="d-none d-lg-block w-25 sticky-top box-main-right">
                    <form @submit.prevent="Search" class="w-100 d-flex">
                        <input type="text" placeholder="Search..." class="rounded-pill shadow-sm px-3 py-2 w-100">
                        <button type="submit" class="btn btn-sm rounded-pill" hidden></button>
                    </form>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="createPost" ref="hiddenmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content px-3">
                        <div class="modal-header pb-0">
                            <h5 class="modal-title"><i class="fas fa-feather-alt mr-1" style="color:#ff7e67;"></i>create</h5>
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
</template>

<script>
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
                content: '',
                image: '',
            }
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
        mounted(){
            $(this.$refs.hiddenmodal).on("hidden.bs.modal", this.closeModal)
        }
    }
</script>