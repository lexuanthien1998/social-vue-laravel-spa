<template>
<div>
    <!-- <router-view></router-view>    -->
    <div class="box-content shadow-sm">
        <div class="row px-3 py-2 box-post">
            <!-- avatar + name -->
            <div class="col d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img :src="post.image_profile != '' ? post.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="rounded-circle img-fluid">
                    <!-- <h1 class="text-img" v-bind:style="{backgroundImage: post.image_profile != '' ? `url('` + post.image_profile + `')` : `url('/images/user.png')` }">{{ post.username | username() }}</h1> -->
                    <div>
                        <router-link :to="{name: 'profile', params: { username: post.username } }"><div class="name-user-post">{{post.username}}</div></router-link>
                        <p class="datatime-post">{{dateFormat(post.created_at)}}</p>
                    </div>
                </div>
                <div class="dropdown">
                    <h5 data-toggle="dropdown"><i class="far fa-ellipsis-h"></i></h5>
                    <div class="dropdown-menu dropdown-menu-right mt-1 px-2 shadow">
                        <i v-if="post.user_id == user.id" @click="editPost()" class="fas fa-edit dropdown-item"></i>
                        <i v-if="post.user_id == user.id" @click="deletePost()" class="fas fa-trash-alt dropdown-item"></i>
                        <i class="fas fa-link dropdown-item" @click="copyURL()"></i>
                    </div>
                </div>
            </div>
            <!-- action delete + edit -->
            <!-- content + image -->
            <div class="px-3 post-image">
                <span v-if="post.content != null" v-on:click="detailsPost()">{{ post.content }}</span>
                <div class="my-2 bg-images" v-if="post.path != ''" v-on:click="detailsPost()" v-bind:style="{backgroundImage: `url('` + post.path + `')`}"></div>
            </div>
            <!-- icon like... -->
            <div class="px-3">
                <div class="d-flex justify-content-between p-2 box-action-post">
                    <div>
                        <i v-if="post.likes" v-bind:class="[post.likes.includes(user.id) ? isLiked : '']" ref='ref_likes' v-on:click="likesPost()" class="far fa-heart"></i>
                        <label for="comment" class="px-2"><i class="far fa-comment"></i></label>
                        <i class="far fa-share"></i>
                    </div>
                    <div class="d-flex mt-2" v-if="post.likes || post.comments">
                        <p v-if="post.likes.length > 0">{{ post.likes.length }} likes</p>
                        <p v-if="post.comments.length> 0" class="pl-3">{{ post.comments.length }} comments</p>
                    </div>
                </div>
                <!-- input comment -->
                <div class="d-flex align-items-center pb-3">
                    <img :src="user.image_profile != '' ? user.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="rounded-circle img-fluid mr-3">
                    <!-- <h1 class="text-img pr-4" v-bind:style="{backgroundImage: user.image_profile != '' ? `url('` + user.image_profile + `')` : `url('/images/user.png')`}">{{ user.username | username() }}</h1> -->
                    <form @submit.prevent="addComment()" class="w-100">
                        <input type="text" id="comment" ref='ref_comment' placeholder="Add comment..." class="rounded-pill px-3 py-1 w-100 comment">
                        <button type="submit" class="btn btn-sm rounded-pill" hidden></button>
                    </form>
                </div>

                <div class="box-comment" v-if="post.comments">
                    <div class="d-flex" v-for="(item, index) in post.comments" :key="index">
                        <img :src="item.user.image_profile != null ? `../storage/images/users/`+ item.user.id + `/image_profile/` + item.user.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="mt-1 rounded-pill img-fluid">
                        <div class="px-3">
                            <div class="name-user-post m-0">{{item.user.username}}</div>
                            <p v-on:click="likesComment(index)">{{item.comment}}</p>
                        </div>
                    </div>
                </div>
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
                title: this.post.username != undefined ? '@' + this.post.username + ' | ' + this.post.content : 'ʟ ᴏ ɴ ᴇ ʟ ʏ',
            }
        },
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
            dateFormat(date) {
                moment.locale("vi")
               if(moment(date).add(5, 'days').format('L') < moment().format('L')) {
                   return moment(date).format("DD MMM, YYYY");
               } else {
                   return moment(date).format("ddd, HH:mm");
               }
            },
            editPost() {
                this.$router.push({ name: 'home' })
            },
            deletePost() {
                axios
                .post('/api/post/destroy', {
                    id: this.post.id
                })
                .then((response) => {
                    this.$router.push({ name: 'home' })
                })
                .catch((error) => {
                    return
                });
            },
            likesPost() {
                if(this.$refs.ref_likes.classList.contains('is-liked')) {
                    axios
                    .post('/api/post/dislikes', {
                        user_id:this.user.id,
                        post_id: this.post.id
                    })
                    .then((response) => {
                        this.$refs.ref_likes.classList.remove('is-liked')
                        this.post.likes.pop()
                    })
                    .catch((error) => {
                        return
                    });
                } else {
                    axios
                    .post('/api/post/likes', {
                        user_id:this.user.id,
                        post_id: this.post.id
                    })
                    .then((response) => {
                        this.$refs.ref_likes.classList.add('is-liked')
                        this.post.likes.push(this.user.id)
                    })
                    .catch((error) => {
                        return
                    });
                }
            },
            addComment() {
                if(this.$refs.ref_comment.value != '') {
                    if(!this.$store.getters.loggedIn) {
                        this.$router.push({ name: 'login' })
                    }
                    axios
                    .post('/api/post/comment', {
                        user_id:this.user.id,
                        post_id: this.post.id,
                        comment: this.$refs.ref_comment.value
                    })
                    .then((response) => {
                        this.post.comments.push(
                            {'comment':this.$refs.ref_comment.value,
                            'user_id':this.user.id,
                            'created_at': new Date().toISOString(),
                            'user': response.data
                            }
                        )
                        this.$refs.ref_comment.value = ''
                    })
                    .catch((error) => {
                        return
                    });
                }
            },
            likesComment(index) {
                axios
                .post('/api/likes-comment/'+ this.post.comments[index].id, {
                    id: this.user.id,
                })
                .then((response) => {
                   console.log(response.data)
                })
                .catch((error) => {
                    return
                });
            }
        }
    }
</script>