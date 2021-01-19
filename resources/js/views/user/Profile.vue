<template>
    <div>
        <div class="img-main shadow" v-bind:style="{backgroundImage: users.image_main != '' ? `url('` + users.image_main + `')` : `url('/images/bg.jpg')` }">
            <div class="d-flex box-btn">
                <div class="d-flex align-items-center" v-if="user.id != users.id" v-on:click="messages()"><i class="far fa-envelope-open"></i><span class="d-none d-xl-block">messages</span></div>
                <div class="d-flex align-items-center" v-on:click="follow()" v-if="user.id != users.id && !(user.following.includes(users.id))"><i class="fal fa-users-medical"></i><span class="d-none d-xl-block">follow</span></div>
                <div class="d-flex align-items-center" v-on:click="unfollow()" v-if="user.id != users.id && user.following.includes(users.id)"><i class="far fa-user-check"></i><span class="d-none d-xl-block">unfollow</span></div>
                <div class="d-flex align-items-center" v-if="user.id == users.id" v-on:click="editProfile()"><i class="fal fa-user-cog"></i><span class="d-none d-xl-block">editprofile</span></div>
            </div>
        </div>
        <div class="img-profile">
            <div class="rounded-circle shadow" v-bind:style="{backgroundImage: users.image_profile != '' ? `url('` + users.image_profile + `')` : `url('/images/user.png')`}"></div>
        </div>
        <div class="text-center box-name">
            <p>{{user.name}}</p>
        </div>
        <div class="box-list-profile">
            <ul class="list-unstyled d-flex justify-content-evenly pl-0">
                <li v-on:click="postsPage()">Posts</li>
                <li v-on:click="photosPage()">Photos</li>
                <li v-on:click="introPage()">Intro</li>
                <li v-on:click="followersPage()">Followers</li>
                <li v-on:click="followingPage()">Following</li>
            </ul>
        </div>
        <div class="px-8 box-posts">
            <router-view v-if="!active"></router-view>
            <div v-else>
                <div class="box-content shadow-sm bg-white" v-if="user.id == users.id">
                    <form @submit.prevent="submitPost" class="form-post px-4 py-2" enctype="multipart/form-data" ref='create_post'>
                        <div class="form-group d-flex">
                            <img :src="user.image_profile != '' ? user.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="rounded-circle img-fluid mr-3">
                            <textarea class="form-control" ref="content" rows="2" v-model="content"></textarea>
                        </div>
                        <div v-if="image != ''" class="bg-images mb-2" v-bind:style="{backgroundImage: `url('` + image + `')`}" style="max-height:250px;"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="image-upload">
                                <label for="file-input"><i class="far fa-images"></i></label>
                                <input id="file-input" v-on:change="onImageChange" type="file"/>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-sm rounded-pill">Đăng bài</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="box-content shadow-sm" v-for="(post, index) in items" :key="index">
                    <div class="row px-3 py-2 box-post">
                        <!-- avatar + name -->
                        <div class="col d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img :src="users.image_profile != '' ? users.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="rounded-circle img-fluid">
                                <div>
                                    <div class="name-user-post">{{users.username}}</div>
                                    <p class="datatime-post">{{dateFormat(post.created_at)}}</p>
                                </div>
                            </div>
                            <div class="dropdown">
                                <h5 data-toggle="dropdown"><i class="far fa-ellipsis-h"></i></h5>
                                <div class="dropdown-menu dropdown-menu-right mt-1 px-2 shadow">
                                    <i v-if="post.user_id == user.id" @click="editPost(index)" class="fas fa-edit dropdown-item"></i>
                                    <i v-if="post.user_id == user.id" @click="deletePost(index)" class="fas fa-trash-alt dropdown-item"></i>
                                    <i class="fas fa-link dropdown-item" @click="copyURL(index)"></i>
                                </div>
                            </div>
                        </div>
                        <!-- action delete + edit -->
                        <!-- content + image -->
                        <div class="px-3 post-image">
                            <span v-if="post.content != null" v-on:click="detailsPost(index)">{{ post.content | shortText(250) }}</span>
                            <span class="d-none" v-if="post.content != null" v-on:click="detailsPost(index)">{{ post.content }}</span>
                            <div v-if="post.content != null && post.content.length > 250" v-on:click="toggler($event)" class="see-more">See more</div>
                            <div class="my-2 bg-images" v-if="post.path != ''" v-on:click="detailsPost(index)" v-bind:style="{backgroundImage: `url('` + post.path + `')`}"></div>
                        </div>
                        <!-- icon like... -->
                        <div class="px-3">
                            <div class="d-flex justify-content-between p-2 box-action-post">
                                <div>
                                    <i v-bind:class="[post.likes.includes(user.id) ? isLiked : '']" ref='ref_likes' v-on:click="likesPost(index)" class="far fa-heart"></i>
                                    <label v-bind:for="'comment' + index" class="px-2"><i class="far fa-comment"></i></label>
                                    <i class="far fa-share"></i>
                                </div>
                                <div class="d-flex mt-2">
                                    <p v-if="post.likes.length > 0">{{ post.likes.length }} likes</p>
                                    <p v-if="post.comments.length> 0" class="pl-3">{{ post.comments.length }} comments</p>
                                </div>
                            </div>
                            <!-- input comment -->
                            <div class="d-flex align-items-center pb-2">
                                <img :src="user.image_profile != '' ? user.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="rounded-circle img-fluid mr-3">
                                <form @submit.prevent="addComment(index)" class="w-100">
                                    <input type="text" v-bind:id="'comment' + index" ref='ref_comment' placeholder="Add comment..." class="rounded-pill px-3 py-1 w-100 comment">
                                    <button type="submit" class="btn btn-sm rounded-pill" hidden></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal" ref="modalEmpty" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-custom">
                        <div class="modal-content text-center py-3">
                            <h4 class="modal-title py-4">itempty!</h4>
                            <div class="d-flex justify-content-evenly">
                                <p v-on:click="cancelEdit()">cancel</p>
                                <p v-on:click="continueEdit()">continue</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Show Post -->
                <div class="modal show-post" ref="modalShowPost">
                    <i class="fa fa-times sticky-top position-fixed icon-close" v-on:click="close()"></i>
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content box-pc d-none d-lg-block">
                            <div class="modal-body d-flex" v-if="info != ''">
                                <div class="w-75 box-image" v-bind:style="{backgroundImage: `url('` + info.path + `')`}"></div>
                                <div class="w-25 overflow-scroll box-info">
                                    <div class="d-flex justify-content-between align-items-center pb-2">
                                        <div class="d-flex align-items-center">
                                            <img :src="users.image_profile != '' ? users.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" :class="users.image_profile != '' ? 'border border-2' : ''" class="img-fluid">
                                            <div>
                                                <div class="name-user-post">{{users.username}}</div>
                                                <p class="datatime-post">{{dateFormat(info.created_at)}}</p>
                                            </div>
                                        </div>
                                        <i class="far fa-ellipsis-h" data-toggle="dropdown"></i>
                                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                            <i v-if="info.user_id == user.id" @click="editPost(info_index)" class="fas fa-edit dropdown-item"></i>
                                            <i v-if="info.user_id == user.id" @click="deletePost(info_index)" class="fas fa-trash-alt dropdown-item"></i>
                                            <i class="fas fa-link dropdown-item" @click="copyURL(info_index)"></i>
                                        </div>
                                    </div>

                                    <p class="m-0 p-content pb-2" v-if="info.content != null">{{info.content}}</p>
                                    
                                    <div class="d-flex pt-2 box-action" v-if="info != ''">
                                        <i :ref="'ref_likes' + info_index" v-bind:class="[info.likes.includes(user.id) ? isLiked : '']" v-bind:style="{color: info.likes.includes(user.id) ? '#ec524b' : ''}" v-on:click="likesPost(info_index)" class="far fa-heart"></i>
                                        <label for="comment"><i class="far fa-comment px-4"></i></label>
                                        <i class="far fa-share"></i>
                                    </div>

                                    <div class="d-flex pb-2" v-if="info.likes.length > 0 || info.comments.length > 0">
                                        <p class="m-0 pr-3" v-if="info.likes.length > 0">{{ info.likes.length }} likes</p>
                                        <p class="m-0" v-if="info.comments.length > 0">{{ info.comments.length }} comments</p>
                                    </div>

                                    <div class="d-flex align-items-center pb-2">
                                        <img :src="user.image_profile != '' ? user.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" :class="user.image_profile != '' ? 'border border-2' : ''" class="img-fluid mr-2">
                                        <form @submit.prevent="modalComment(info_index)" class="w-100">
                                            <input type="text" id="comment" ref="comment" placeholder="Add comment..." class="w-100 border border-2">
                                            <button type="submit" class="btn btn-sm rounded-pill" hidden></button>
                                        </form>
                                    </div>

                                    <div class="box-comment" v-if="info.comments.length > 0">
                                        <div class="d-flex pb-2" v-for="(item, index) in info.comments" :key="index">
                                            <img :src="item.user.image_profile != null ? `../storage/images/users/`+ item.user.id + `/image_profile/` + item.user.image_profile : `/images/user.png`" :class="item.user.image_profile != null ? 'border border-2' : ''" style="width:35px; height:35px; vertical-align: middle;" class="img-fluid mt-1">
                                            <div>
                                                <div class="name-user-post px-2 m-0">{{item.user.username}}</div>
                                                <p class="px-2">{{item.comment}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-content box-sp d-block d-lg-none">
                            <div class="modal-body" v-if="info != ''">
                                <div class="box-image" v-bind:style="{backgroundImage: `url('` + info.path + `')`}"></div>
                                <div class="box-info px-0" style="background:#fff; height:auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img :src="users.image_profile != '' ? users.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" :class="users.image_profile != '' ? 'border border-2' : ''" class="img-fluid">
                                            <div>
                                                <div class="name-user-post">{{users.username}}</div>
                                                <p class="datatime-post">{{dateFormat(info.created_at)}}</p>
                                            </div>
                                            <i class="far fa-ellipsis-h ml-3" data-toggle="dropdown"></i>
                                            <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                                <i v-if="info.user_id == user.id" @click="editPost(info_index)" class="fas fa-edit dropdown-item"></i>
                                                <i v-if="info.user_id == user.id" @click="deletePost(info_index)" class="fas fa-trash-alt dropdown-item"></i>
                                                <i class="fas fa-link dropdown-item" @click="copyURL(info_index)"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center box-action" v-if="info != ''">
                                            <i :ref="'ref_likes' + info_index" v-bind:class="[info.likes.includes(user.id) ? isLiked : '']" v-bind:style="{color: info.likes.includes(user.id) ? '#ec524b' : ''}" v-on:click="likesPost(info_index)" class="far fa-heart"></i>
                                            <i class="far fa-share"></i>
                                        </div>
                                    </div>
                                    <div class="d-flex pt-2" v-if="info.likes.length > 0 || info.comments.length > 0">
                                        <p class="m-0 pr-3" v-if="info.likes.length > 0">{{ info.likes.length }} likes</p>
                                        <p class="m-0" v-if="info.comments.length > 0">{{ info.comments.length }} comments</p>
                                    </div>
                                    <div class="d-flex pt-2 align-items-center">
                                        <img :src="user.image_profile != '' ? user.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="img-fluid mr-2 border border-2">
                                        <form @submit.prevent="modalCommentSP(info_index)" class="w-100">
                                            <input type="text" id="comment" ref="comment_sp" placeholder="Add comment..." class="w-100 border border-2">
                                            <button type="submit" class="btn btn-sm rounded-pill" hidden></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal" ref="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content px-3 box-edit">
                    <div class="modal-header pb-0 d-flex justify-content-end">
                        <i class="fas fa-times" data-bs-dismiss="modal"></i>
                    </div>
                    <form @submit.prevent="updateProfile" class="form-post p-3" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="img-main shadow" v-bind:style="{backgroundImage: users.image_main != '' ? `url('` + users.image_main + `')` : `url('/images/bg.jpg')`}" style="max-height:150px;">
                                <label for="image-main">
                                    <div class="d-flex box-btn box-camera"><i class="fas fa-camera icon-camera"></i></div>
                                </label>
                                <input id="image-main" v-on:change="mainImageChange" type="file" hidden/>
                            </div>

                            <label for="image-profile" class="image-profile">
                                <div class="rounded-circle shadow border border-5" v-bind:style="{backgroundImage: users.image_profile != '' ? `url('` + users.image_profile + `')` : `url('/images/user.png')`}"></div>
                                <input id="image-profile" v-on:change="profileImageChange" type="file" hidden/>
                            </label>

                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-signature"></i>
                                <input type="text" v-model="users.name"  placeholder="your name." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-user-edit"></i>
                                <input type="text" v-model="users.username" placeholder="your username." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-envelope"></i>
                                <input type="text" v-model="users.email" placeholder="your email." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-phone"></i>
                                <input type="text" v-model="users.phone_number" placeholder="your phone number." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" v-model="users.address" placeholder="your address." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-address-book"></i>
                                <input type="text" v-model="users.description" placeholder="description about you." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <template>
                                <v-row>
                                    <v-col class="d-flex" cols="12" sm="6">
                                        <v-select 
                                        prepend-icon="mdi-gender-male-female" 
                                        v-model="users.gender" 
                                        :items="gender" 
                                        dense
                                        item-text="name"
                                        item-value="value"
                                        persistent-hint
                                        return-item-value
                                        single-line
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template>
                                <v-row>
                                    <v-col cols="12" sm="6" md="5">
                                        <v-menu ref="menu" v-model="menu" :close-on-content-click="false"
                                            :return-value.sync="date" transition="scale-transition" offset-y min-width="290px">
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field v-model="date" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
                                            </template>
                                            <v-date-picker v-model="date" no-title scrollable >
                                            <v-spacer></v-spacer>
                                            <v-btn text color="primary" @click="menu = false">Cancel</v-btn>
                                            <v-btn text color="primary" @click="$refs.menu.save(date)" >OK
                                            </v-btn>
                                            </v-date-picker>
                                        </v-menu>
                                    </v-col>
                                </v-row>
                            </template>
                        </div>
                        <div class="px-2" v-if="message != ''">
                            <p class="text-message rounded-pill">{{message}}</p>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="submit" class="btn rounded-pill p-2 w-25">Save</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</template>

<script>
    import moment from 'moment';
    export default {
        metaInfo () {
            return {
                title: this.users.name != '' ? this.users.name + ' (@' + this.users.username + ') / ʟ ᴏ ɴ ᴇ ʟ ʏ' : '@'+this.users.username + ' / ʟ ᴏ ɴ ᴇ ʟ ʏ',
            }
        },
        data() {
            return {
                user:this.$store.getters.getUser,
                users: {
                    id: '',
                    email: '',
                    username: '',
                    name: '',
                    gender: '',
                    address: '',
                    description: '',
                    phone_number: '',
                    birth_date: '',
                    image_main: '',
                    image_profile: '',
                    followers: '',
                    following: '',
                },
                message: '',
                gender: [
                    { name: 'male', value: '1' },
                    { name: 'female', value: '2' },
                    { name: 'null (private)', value: '0' },
                ],
                date: new Date().toISOString().substr(0, 10),
                menu: false,
                modal: false,
                // posts
                content: '',
                image: '',
                posts: [],
                isLiked: 'is-liked',
                isEdit: null,
                info: [],
                info_index: '',
                active: true
            }
        },
        mounted() {
            axios
            .get('/api/user/'+this.$route.params.username)
            .then(response => {
                this.users.id = response.data.user.id
                this.users.email = response.data.user.email
                this.users.username = response.data.user.username
                this.users.name = response.data.user.name != null ? response.data.user.name : ''
                this.users.gender = response.data.user.gender
                this.users.address = response.data.user.address != null ? response.data.user.address : ''
                this.users.description = response.data.user.description != null ? response.data.user.description : ''
                this.users.phone_number = response.data.user.phone_number != null ? response.data.user.phone_number : ''
                this.users.birth_date = response.data.user.birth_date != null ? response.data.user.birth_date : ''
                this.users.image_main = response.data.user.image_main != null ? response.data.user.image_main : ''
                this.users.image_profile = response.data.user.image_profile != null ? response.data.user.image_profile : ''
                this.users.followers = response.data.followers
                this.users.following = response.data.following
                this.date = response.data.user.birth_date != null ? response.data.user.birth_date : this.date
                this.posts = response.data.posts
            })
            .catch(error => {
                this.$router.back()
            });
        },
        watch: {
            $route(to) {
                axios
                .get('/api/user/'+ to.params.username)
                .then(response => {
                    this.users.id = response.data.user.id
                    this.users.email = response.data.user.email
                    this.users.username = response.data.user.username
                    this.users.name = response.data.user.name != null ? response.data.user.name : ''
                    this.users.gender = response.data.user.gender
                    this.users.address = response.data.user.address != null ? response.data.user.address : ''
                    this.users.description = response.data.user.description != null ? response.data.user.description : ''
                    this.users.phone_number = response.data.user.phone_number != null ? response.data.user.phone_number : ''
                    this.users.birth_date = response.data.user.birth_date != null ? response.data.user.birth_date : ''
                    this.users.image_main = response.data.user.image_main != null ? response.data.user.image_main : ''
                    this.users.image_profile = response.data.user.image_profile != null ? response.data.user.image_profile : ''
                    this.users.followers = response.data.followers
                    this.users.following = response.data.following
                    this.date = response.data.user.birth_date != null ? response.data.user.birth_date : this.date
                    this.posts = response.data.posts
                })
                .catch(error => {
                    this.$router.back()
                });
            }
        },
        computed: {
            items() {
                return this.posts.reverse()
            }
        },
        methods: {
            dateFormat(date) {
                moment.locale("vi")
               if(moment(date).add(5, 'days').format('L') < moment().format('L')) {
                   return moment(date).format("DD MMM, YYYY");
               } else {
                   return moment(date).format("ddd, HH:mm");
               }
            },
            postsPage() {
                this.active = true
                this.$router.push({name: 'profile', params: { username : this.users.username } });
            },
            photosPage() {
                this.active = false
                this.$router.push({ name: 'photos' })
            },
            introPage() {
                this.active = false
                this.$router.push({ name: 'intro' })
            },
            followersPage() {
                this.active = false
                this.$router.push({ name: 'followers' })
            },
            followingPage() {
                this.active = false
                this.$router.push({ name: 'following' })
            },
            mainImageChange(e){
                if (e.target.files[0]) {
                    let data = this
                    if (e.target.files[0].type.slice(0, 6) === 'image/') {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            data.users.image_main = e.target.result;
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    }
                }
            },
            profileImageChange(e){
                if (e.target.files[0]) {
                    let data = this
                    if (e.target.files[0].type.slice(0, 6) === 'image/') {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            data.users.image_profile = e.target.result;
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    }
                }
            },
            editProfile() {
                $(this.$refs.modal).modal('show')
            },
            updateProfile(e) {
                this.users.birth_date = this.date
                e.preventDefault();
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                axios.post('/api/user/'+this.users.id+'/profile/update', {
                    data: this.users,
                })
                .then((response) => {
                    this.$store.dispatch('reloadUser', this.users)
                    $(this.$refs.modal).modal('toggle')
                    this.$router.push({ name: 'home' }).catch(()=>{})
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
                    return
                });
            },
            follow() {
                axios.post('/api/follow', {
                    user_id: this.user.id,
                    id: this.users.id,
                })
                .then((response) => {
                    this.user.following.push(this.users.id)
                    this.$store.dispatch('reloadUser', this.user)
                })
                .catch((error) => {
                    return
                });
            },
            unfollow() {
                axios.post('/api/unfollow', {
                    user_id: this.user.id,
                    id: this.users.id,
                }) 
                .then((response) => {
                    const array = this.user.following.filter(item => item !== this.users.id);
                    this.user.following = array
                    this.$store.dispatch('reloadUser', this.user)
                })
                .catch((error) => {
                    return
                });
            },
            // posts
            detailsPost(index) {
                if(this.posts[index].path == '') {
                    var id = this.posts[index].id;
                    this.$router.push({ name: 'post-details', params: { id } }).catch(()=>{});
                } else {
                    $(this.$refs.modalShowPost).modal('show');
                    this.info = this.posts[index]
                    this.info_index = index
                }
            },
            close() {
                $(this.$refs.modalShowPost).modal('toggle');
                this.info = ''
                this.info_index = ''
            },
            onImageChange(e){
                if (e.target.files[0]) {
                    let data = this
                    if (e.target.files[0].type.slice(0, 6) === 'image/') {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            data.image = e.target.result;
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    }
                }
            },
            submitPost(e) {
                e.preventDefault();
                if(!this.$store.getters.loggedIn) {
                    this.$router.push({ name: 'login' })
                }
                if(this.isEdit != null) {
                    if(this.content == '' && this.image == '') {
                        $(this.$refs.modalEmpty).modal('show')
                    } else {
                        axios.post('/api/post/'+ this.posts[this.isEdit].id +'/update', {
                            content: this.content,
                            image: this.image,
                        })
                        .then((response) => {
                            this.posts[this.isEdit].content = this.content
                            this.posts[this.isEdit].path = this.image
                            this.image = ''
                            this.content = ''
                            this.isEdit = null
                        })
                        .catch((error) => {
                            return
                        });
                    }
                } else {
                    if(this.image != '' || this.content != '') {
                        axios.post('/api/post/store', {
                            user_id: this.user.id,
                            content: this.content,
                            image: this.image,
                        })
                        .then((response) => {
                            this.posts.push({
                                'id':response.data.id,
                                'username':this.user.username,
                                'content':this.content,
                                'path':this.image,
                                'comments': [],
                                'likes': [],
                                'created_at': new Date().toISOString(),
                                'user_id':this.user.id,
                                'image_profile': this.user.image_profile
                            })
                            this.image = ''
                            this.content = ''
                        })
                        .catch((error) => {
                            if(error.response) {
                                console.log(error.response.data.message);
                            } else {
                                console.log(error)
                            }
                        });
                    }
                }
            },
            editPost(index) {
                if($(this.$refs.modalShowPost).hasClass('show')) {
                    $(this.$refs.modalShowPost).modal('toggle');
                }
                this.image = this.posts[index].path
                this.content = this.posts[index].content
                this.$refs['create_post'].scrollIntoView(0,0)
                this.isEdit = index
            },
            cancelEdit() {
                this.image = ''
                this.content = ''
                this.isEdit = null
                $(this.$refs.modalEmpty).modal('toggle')
            },
            continueEdit() {
                $(this.$refs.modalEmpty).modal('toggle')
                this.$refs['content'].focus()
            },
            deletePost(index) {
                axios
                .post('/api/post/destroy', {
                    id: this.posts[index].id
                })
                .then((response) => {
                    this.posts.splice(index, 1)
                    if($(this.$refs.modalShowPost).hasClass('show')) {
                        $(this.$refs.modalShowPost).modal('toggle');
                    }
                })
                .catch((error) => {
                    return
                });
            },
            copyURL(index) {
                const path = this.$router.resolve({
                    name: "post-details",
                    params: { id: this.posts[index].id }
                }).href;
                const fullUrl = window.location.origin + path;
                navigator.clipboard.writeText(fullUrl);
            },
            likesPost(index) {
                if(this.$refs.ref_likes[index].classList.contains('is-liked')) {
                    axios
                    .post('/api/post/dislikes', {
                        user_id:this.user.id,
                        post_id: this.posts[index].id
                    })
                    .then((response) => {
                        this.$refs.ref_likes[index].classList.remove('is-liked')
                        this.posts[index].likes.pop()
                    })
                    .catch((error) => {
                        return
                    });
                } else {
                    axios
                    .post('/api/post/likes', {
                        user_id:this.user.id,
                        post_id: this.posts[index].id
                    })
                    .then((response) => {
                        this.$refs.ref_likes[index].classList.add('is-liked')
                        this.posts[index].likes.push(this.user.id)
                    })
                    .catch((error) => {
                        return
                    });
                }
            },
            addComment(index) {
                if(this.$refs.ref_comment[index].value != '') {
                    if(!this.$store.getters.loggedIn) {
                        this.$router.push({ name: 'login' })
                    }
                    axios
                    .post('/api/post/comment', {
                        user_id:this.user.id,
                        post_id: this.posts[index].id,
                        comment: this.$refs.ref_comment[index].value
                    })
                    .then((response) => {
                        this.posts[index].comments.push(
                            {'comment':this.$refs.ref_comment[index].value,
                            'user_id':this.user.id,
                            'created_at': new Date().toISOString(),
                            'user': response.data
                            }
                        )
                        this.$refs.ref_comment[index].value = ''
                    })
                    .catch((error) => {
                        return
                    });
                }
            },
            modalComment(index) {
                if(this.$refs.comment.value != '') {
                    if(!this.$store.getters.loggedIn) {
                        this.$router.push({ name: 'login' })
                    }
                    axios
                    .post('/api/post/comment', {
                        user_id:this.user.id,
                        post_id: this.posts[index].id,
                        comment: this.$refs.comment.value
                    })
                    .then((response) => {
                        this.posts[index].comments.push(
                            {'comment':this.$refs.comment.value,
                            'user_id':this.user.id,
                            'created_at': new Date().toISOString(),
                            'user': response.data
                            }
                        )
                        this.$refs.comment.value = ''
                    })
                    .catch((error) => {
                        return
                    });
                }
            },
            modalCommentSP(index) {
                if(this.$refs.comment_sp.value != '') {
                    if(!this.$store.getters.loggedIn) {
                        this.$router.push({ name: 'login' })
                    }
                    axios
                    .post('/api/post/comment', {
                        user_id:this.user.id,
                        post_id: this.posts[index].id,
                        comment: this.$refs.comment_sp.value
                    })
                    .then((response) => {
                        this.posts[index].comments.push(
                            {'comment':this.$refs.comment_sp.value,
                            'user_id':this.user.id,
                            'created_at': new Date().toISOString(),
                            'user': response.data
                            }
                        )
                        this.$refs.comment_sp.value = ''
                    })
                    .catch((error) => {
                        return
                    });
                }
            },
            toggler(e) {
                if(e.target.innerHTML == 'See more') {
                    e.target.innerHTML = 'Less more';
                    e.target.parentElement.children[0].className = '';
                    e.target.parentElement.children[0].className = 'd-none';
                    e.target.parentElement.children[1].className = '';
                    e.target.parentElement.children[1].className = 'd-block';
                } else {
                    e.target.innerHTML = 'See more';
                    e.target.parentElement.children[0].className = '';
                    e.target.parentElement.children[0].className = 'd-block';
                    e.target.parentElement.children[1].className = '';
                    e.target.parentElement.children[1].className = 'd-none';
                }
            },
            messages() {
                this.axios.post('/api/add-contact', {
                    id: this.users.id,
                }, {
                    'headers' : {
                        'Accept' : 'application/json',
                        'Authorization' : 'Bearer '+this.$store.getters.token,
                    } 
                })
                .then((response) => {
                    this.$router.push({ name: 'messages' })
                })
                .catch((error) => {
                    return
                });
            }
        },
        filters: {
            shortText(value, limit) {
                if(value) {
                    if(value.length >= 250) {
                        return value.substr(0,value.indexOf(' ')+limit) + '...';
                    } else {
                        return value;
                    }
                }
            },
            username(value) {
                return value.substr(value.length - 2, value.length);
            }
        }
    }
</script>