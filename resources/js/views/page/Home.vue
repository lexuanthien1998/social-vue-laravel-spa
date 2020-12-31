<template>
<div>
    <div class="box-content shadow-sm bg-white">
        <form @submit.prevent="submitPost" class="form-post px-4 py-2" enctype="multipart/form-data">
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
                <button type="submit" class="btn rounded-pill">Đăng bài</button>
            </div>
        </form>
    </div>

    <div class="box-content shadow-sm" v-for="(post, index) in items" :key="index">
        <div class="row px-3 py-2 box-post">
            <!-- avatar + name -->
            <div class="col d-flex">
                <img src="/images/avatar.jpg" style="max-height:30px;" class="rounded-circle img-fluid">
                <div class="mt-1 name-user-post">{{post.username}}</div>
            </div>
            <!-- action delete + edit -->
            <div class="col d-flex justify-content-end">
                <div class="dropdown">
                    <h5 data-toggle="dropdown"><i class="far fa-ellipsis-h"></i></h5>
                    <div class="dropdown-menu dropdown-menu-right mt-1 px-2 shadow">
                        <i class="fas fa-edit dropdown-item"></i>
                        <i @click="deletePost(index)" class="fas fa-trash-alt dropdown-item"></i>
                        <i class="fas fa-link dropdown-item"></i>
                    </div>
                </div>
            </div>
            <!-- content + image -->
            <div class="px-3 post-image">
                <span v-if="post.content != null" v-on:click="detailsPost(index)">{{ post.content | shortText(250) }}</span>
                <span class="d-none" v-if="post.content != null" v-on:click="detailsPost(index)">{{ post.content }}</span>
                <div v-if="post.content != null && post.content.length > 250" v-on:click="toggler($event)" class="see-more">See more</div>

                <!-- <figure class="mt-2" v-on:click="detailsPost(index)">
                    <img v-bind:src="post.path" class="img-fluid" alt="">
                </figure> -->
                <!-- <div class="text-center">
                    <h1 class="text-img" v-bind:style="{backgroundImage: bgIMG(post.path)}">lonely</h1>
                </div> -->
                <div class="my-3 bg-images" v-bind:style="{backgroundImage: bgIMG(post.path)}">
                    
                </div>
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
                <div class="d-flex pb-2">
                    <img :src="user.image_profile != '' ? user.image_profile : `/images/avatar.jpg`" style="width:35px; height:35px; vertical-align: middle;" class="rounded-circle img-fluid border border-1 mr-2">
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
                .get('/api/post/index')
                .then(response => {
                    this.posts = response.data.posts
                })
                .catch(response => {
                });
        },
        computed: {
            items() {
                return this.posts.reverse()
            },
        },
        methods: {
            bgIMG(base64) {
                return `url('` + base64 + `')`
            },
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
            detailsPost(index) {
                var id = this.posts[index].id;
                this.$router.push({ name: 'post-details', params: { id } }).catch(()=>{});
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
                            'created_at': new Date().toISOString()}
                        )
                        this.$refs.ref_comment[index].value = ''
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
            }
        }
    }
</script>