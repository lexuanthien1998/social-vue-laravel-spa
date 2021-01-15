<template>
<div style="padding-bottom:0.1px;">
    <div class="box-content shadow-sm bg-white">
        <form @submit.prevent="submitPost" class="form-post px-4 py-2" enctype="multipart/form-data" ref='create_post'>
            <div class="form-group d-flex">
                <div class="mr-3">
                    <h1 class="text-img" v-bind:style="{backgroundImage: user.image_profile != '' ? `url('` + user.image_profile + `')` : `url('/images/user.png')` }">{{ user.username | username() }}</h1>
                </div>
                <!-- <img :src="user.image_profile != '' ? user.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="rounded-circle img-fluid mr-3"> -->
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
                    <img :src="post.image_profile != '' ? post.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="rounded-circle img-fluid">
                    <!-- <h1 class="text-img" v-bind:style="{backgroundImage: post.image_profile != '' ? `url('` + post.image_profile + `')` : `url('/images/user.png')` }">{{ post.username | username() }}</h1> -->
                    <router-link :to="{name: 'profile', params: { username: post.username } }">
                        <div class="name-user-post">{{post.username}}</div>
                    </router-link>
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
                    <!-- <h1 class="text-img pr-4" v-bind:style="{backgroundImage: user.image_profile != '' ? `url('` + user.image_profile + `')` : `url('/images/user.png')`}">{{ user.username | username() }}</h1> -->
                    <form @submit.prevent="addComment(index)" class="w-100">
                        <input type="text" v-bind:id="'comment' + index" ref='ref_comment' placeholder="Add comment..." class="rounded-pill px-3 py-1 w-100 comment">
                        <button type="submit" class="btn btn-sm rounded-pill" hidden></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal" ref="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
    <!-- Modal Create Post -->
    <div class="modal box-create-post" ref="modalCreatePost" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content px-3">
                <div class="modal-header pb-0">
                    <h5 class="modal-title"><i class="fas fa-feather-alt mr-1" style="color:#ff7e67;"></i>create...</h5>
                    <i class="fas fa-times" v-on:click="closeModal()"></i>
                </div>
                <form @submit.prevent="submitPostModal" class="form-post p-3" enctype="multipart/form-data">
                    <div class="modal-body">
                        <textarea class="form-control" rows="3" v-model="content_modal"></textarea>
                        <div v-if="image_modal != ''" class="bg-images mt-3" v-bind:style="{backgroundImage: `url('` + image_modal + `')`}" style="max-height:250px;"></div>
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
                                <img :src="info.image_profile != '' ? info.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" :class="info.image_profile != '' ? 'border border-2' : ''" class="img-fluid">
                                <div class="name-user-post">{{info.username}}</div>
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
                                <img :src="info.image_profile != '' ? info.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" :class="info.image_profile != '' ? 'border border-2' : ''" class="img-fluid">
                                <div class="name-user-post">{{info.username}}</div>
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
                            <img :src="user.image_profile != '' ? user.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" :class="user.image_profile != '' ? 'border border-2' : ''" class="img-fluid mr-2">
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
</template>
<script>
    export default {
        metaInfo () {
            return {
                title: 'ʟ ᴏ ɴ ᴇ ʟ ʏ',
            }
        },
        data() {
            return {
                user:this.$store.getters.getUser,
                content: '',
                image: '',
                content_modal: '',
                image_modal: '',
                posts: [],
                isLiked: 'is-liked',
                isEdit: null,
                info: [],
                info_index: '',
            }
        },
        mounted() {
            this.axios
            .get('/api/post/index')
            .then(response => {
                this.posts = response.data.posts
            })
            .catch(response => {
            });

            // Modal Create Post
            if(this.$route.query.action == 'create') {
                $(this.$refs.modalCreatePost).modal('show')
            };
        },
        watch: {
            // Modal Create Post
            $route(to) {
                if(to.query.action == 'create') {
                    $(this.$refs.modalCreatePost).modal('show')
                }
            }
        },
        computed: {
            items() {
                return this.posts.reverse()
            }
        },
        methods: {
            detailsPost(index) {
                if(this.posts[index].path == '') {
                    var id = this.posts[index].id;
                    this.$router.push({ name: 'post-details', params: { id } }).catch(()=>{});
                } else {
                    $(this.$refs.modalShowPost).modal('show');
                    this.info = this.posts[index]
                    this.info_index = index
                }

                // var i = new Image(); 
                // i.onload = function(){
                //     // console.log(i.width, i.height)
                // };
                // i.src = this.info.path; 
                // this.matchHeight(i.width,i.height);
            },
            close() {
                $(this.$refs.modalShowPost).modal('toggle');
                this.info = ''
                this.info_index = ''
            },
            onImageChangeModal(e){
                if (e.target.files[0]) {
                    let data = this
                    if (e.target.files[0].type.slice(0, 6) === 'image/') {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            data.image_modal = e.target.result;
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    }
                }
            },
            submitPostModal(e) {
                e.preventDefault();
                if(this.image_modal != '' || this.content_modal != '') {
                    if(!this.$store.getters.loggedIn) {
                        this.$router.push({ name: 'login' })
                    }
                    axios.post('/api/post/store', {
                        user_id: this.user.id,
                        content: this.content_modal,
                        image: this.image_modal,
                    })
                    .then((response) => {
                        this.posts.push({
                            'id':response.data.id,
                            'username':this.user.username,
                            'content':this.content_modal,
                            'path':this.image_modal,
                            'comments': [],
                            'likes': [],
                            'created_at': new Date().toISOString(),
                            'user_id':this.user.id,
                            'image_profile': this.user.image_profile
                        })
                        this.image_modal = '';
                        this.content_modal = '';
                        //detele query router
                        let query = Object.assign({}, this.$route.query);
                        delete query.action;
                        this.$router.replace({ query });
                        $(this.$refs.modalCreatePost).modal('toggle');
                    })
                    .catch((error) => {
                        //detele query router
                        let query = Object.assign({}, this.$route.query);
                        delete query.action;
                        this.$router.replace({ query });
                        $(this.$refs.modalCreatePost).modal('toggle');
                        return
                    });
                }
            },
            closeModal() {
                //detele query router
                let query = Object.assign({}, this.$route.query);
                delete query.action;
                this.$router.replace({ query });

                this.image_modal = '';
                this.content_modal = '';
                $(this.$refs.modalCreatePost).modal('toggle');
            },
            // End Modal Create Post
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
                        $(this.$refs.modal).modal('show')
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
                $(this.$refs.modal).modal('toggle')
            },
            continueEdit() {
                $(this.$refs.modal).modal('toggle')
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