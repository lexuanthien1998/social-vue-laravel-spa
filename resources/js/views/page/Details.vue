<template>
<div>
    <router-view></router-view>   
    <div class="box-content shadow-sm">
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
                        <a @click="deletePost" class="dropdown-item">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                        <a href="" class="dropdown-item">
                            <i class="fas fa-link"></i> Copy URL Post
                        </a>
                    </div>
                </div>
            </div>
            <!-- content + image -->
            <div class="p-3 post-image">
                <p>{{post.content}}</p>
                <figure>
                    <img v-bind:src="post.path" class="img-fluid" style="border-radius:10px;" alt="">
                </figure>
            </div>
            <!-- icon like... -->
            <div class="px-3">
                <div class="d-flex justify-content-around p-1 box-action-post">
                    <div>
                        <i v-if="post.likes" v-bind:class="[post.likes.includes(user.id) ? isLiked : '']" ref='ref_likes' v-on:click="likesPost" class="far fa-heart"></i>Like
                    </div>

                    <label for="comment">
                        <div><i class="far fa-comment"></i>Comment</div>
                    </label>
                    <div><i class="far fa-share"></i>Share</div>
                </div>
                <div class="d-flex py-2">
                    <p v-if="post.likes" class="pr-3">{{ post.likes.length }} lượt thích</p>
                    <p v-if="post.comments">{{ post.comments.length }} bình luận</p>
                </div>
                <!-- input comment -->
                <div class="d-flex">
                    <img src="/images/avatar.jpg" style="max-height:30px;" class="rounded-circle img-fluid mr-2">
                    <form @submit.prevent="addComment" class="w-100">
                        <input type="text" id="comment" ref='ref_comment' placeholder="Add comment..." class="rounded-pill px-3 py-1 w-100 comment">
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
                post:{},
                isLiked: 'is-liked',
            }
        },
        mounted() {
            axios
            .get('/api/post/'+this.$route.params.id+'/details')
            .then(response => {
                this.post = response.data
            })
            .catch(error => {
                this.$router.back()
            });
        },
        methods: {
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