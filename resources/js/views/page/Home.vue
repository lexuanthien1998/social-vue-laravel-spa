<template>
<div>
    <div class="box-content shadow-sm bg-white">
        <form @submit.prevent="submitPost" class="form-post p-3" enctype="multipart/form-data">
            <div class="form-group d-flex">
                <img src="/images/avatar.jpg" style="max-height:30px;" class="rounded-circle img-fluid mr-3">
                <textarea class="form-control" rows="2" v-model="content"></textarea>
            </div>
            <figure>
                <img class="img-fluid mx-auto" id="images" src="" alt="">
            </figure>
            <div class="d-flex justify-content-between">
                <div class="image-upload">
                    <label for="file-input"><i class="far fa-images"></i></label>
                    <input id="file-input" v-on:change="onImageChange" type="file"/>
                </div>
                <button type="submit" class="btn btn-sm rounded-pill">Đăng bài</button>
            </div>
        </form>
    </div>

    <div class="box-content shadow-sm" v-for="(post, index) in items" :key="index">
        <div class="row p-2">
            <!-- avatar + name -->
            <div class="col d-flex">
                <img src="/images/avatar.jpg" style="max-height:30px;" class="rounded-circle img-fluid">
                <div class="mt-1 name-user-post">{{post.name}}</div>
            </div>
            <!-- action delete + edit -->
            <div class="col d-flex justify-content-end">
                <div class="dropdown">
                    <h5 data-toggle="dropdown"><i class="far fa-ellipsis-h"></i></h5>
                    <div class="dropdown-menu dropdown-menu-right mt-2">
                        <a href="" class="dropdown-item">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a @click="deletePost(index)" class="dropdown-item">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                        <a href="" class="dropdown-item">
                            <i class="fas fa-link"></i> Copy URL Post
                        </a>
                    </div>
                </div>
            </div>
            <!-- content + image -->
            <router-link :to="{name: 'post-details', params: { id: post.id, title: post.content }}">
                <div class="p-3 post-image">
                    <p>{{post.content}}</p>
                    <figure>
                        <img v-bind:src="post.path" class="img-fluid" style="border-radius:10px;" alt="">
                    </figure>
                </div>     
            </router-link>
            <!-- icon like... -->
            <div class="px-3">
                <div class="d-flex justify-content-around p-1 box-action-post">
                    <div>
                        <i :class="[post.likes.includes(user.id) ? isLiked : '']" ref='ref_likes' v-on:click="likesPost(index)" class="far fa-heart"></i>Like
                    </div>

                    <label v-bind:for="'comment' + index">
                        <div><i class="far fa-comment"></i>Comment</div>
                    </label>
                    <div><i class="far fa-share"></i>Share</div>
                </div>
                <div class="d-flex py-2">
                    <p class="pr-3">{{ post.likes.length }} lượt thích</p>
                    <p>{{ post.comments.length }} bình luận</p>
                </div>
                <!-- input comment -->
                <div class="d-flex">
                    <img src="/images/avatar.jpg" style="max-height:30px;" class="rounded-circle img-fluid mr-2">
                    <form @submit.prevent="addComment(index)" class="w-100">
                        <input type="text" v-bind:id="'comment' + index" ref='ref_comment' placeholder="Add comment..." class="rounded-pill px-3 py-1 w-100 comment">
                        <button type="submit" class="btn btn-sm rounded-pill" hidden></button>
                    </form>
                </div>
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
                posts: [],
                isLiked: 'is-liked',
            }
        },
        mounted() {
            this.axios
                .get('api/post/index')
                .then(response => {
                    this.posts = response.data.posts
                    console.log(this.posts)
                })
                .catch(response => {
                    console.log(response)
                });
        },
        computed: {
            items() {
                return this.posts.reverse()
            },
        },
        methods: {
            onImageChange(e){
                // this.image = e.target.files[0];
                if (e.target.files[0]) {
                    if (e.target.files[0].type.slice(0, 6) === 'image/') {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                            $('#images').attr('src', e.target.result);
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    }
                }
            },
            submitPost(e) {
                e.preventDefault();
                this.image = $('#images').attr('src');
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
                        this.posts.push({
                            'id':response.data.id,
                            'name':this.user.name,
                            'content':this.content,
                            'path':this.image,
                            'comments': [],
                            'likes': [],
                            'created_at': new Date().toISOString(),
                            'user_id':this.user.id
                        })
                        this.image = ''
                        this.content = ''
                        $('#images').attr('src', '');
                    })
                    .catch((error) => {
                        if(error.response) {
                            console.log(error.response.data.message);
                        } else {
                            console.log(error)
                        }
                    });
                }
            }, 
            deletePost(index) {
                axios
                .post('/api/post/destroy', {
                    id: this.posts[index].id
                })
                .then((response) => {
                    this.posts.splice(index, 1)
                })
                .catch((error) => {
                    return
                });
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
                        this.posts[index].likes.splice(1)
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
                            'created_at': new Date().toISOString()}
                        )
                        this.$refs.ref_comment[index].value = ''
                    })
                    .catch((error) => {
                        return
                    });
                }
            }
        }
    }
</script>