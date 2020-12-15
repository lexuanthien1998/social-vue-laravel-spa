<template>
<div>
    <div class="box-content shadow-sm">
        <div class="row p-2">
            <!-- avatar + name -->
            <div class="col d-flex">
                <img src="/images/avatar.jpg" style="max-height:30px;" class="rounded-circle img-fluid">
                <!-- <div class="mt-1 name-user-post">{{post.name}}</div> -->
            </div>
            <!-- action delete + edit -->
            <div class="col d-flex justify-content-end">
                <div class="dropdown">
                    <h5 data-toggle="dropdown"><i class="far fa-ellipsis-h"></i></h5>
                    <div class="dropdown-menu dropdown-menu-right mt-2">
                        <a href="" class="dropdown-item">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <!-- <a @click="deletePost(index)" class="dropdown-item">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a> -->
                        <a href="" class="dropdown-item">
                            <i class="fas fa-link"></i> Copy URL Post
                        </a>
                    </div>
                </div>
            </div>
            <!-- content + image -->
            <div class="p-3 post-image">
                <!-- <p>{{post.content}}</p>
                <figure>
                    <img v-bind:src="post.path" class="img-fluid" style="border-radius:10px;" alt="">
                </figure> -->
            </div>
            <!-- icon like... -->
            <div class="px-3">
                <!-- <div class="d-flex justify-content-around p-1 box-action-post">
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
                </div> -->
                <!-- input comment -->
                <!-- <div class="d-flex">
                    <img src="/images/avatar.jpg" style="max-height:30px;" class="rounded-circle img-fluid mr-2">
                    <form @submit.prevent="addComment(index)" class="w-100">
                        <input type="text" v-bind:id="'comment' + index" ref='ref_comment' placeholder="Add comment..." class="rounded-pill px-3 py-1 w-100 comment">
                        <button type="submit" class="btn btn-sm rounded-pill" hidden></button>
                    </form>
                </div> -->
            </div>
        </div>
    </div>
</div>
</template>
<script>
    export default {
        // data() {
        //     return {
        //         user:this.$store.getters.getUser,
        //         post:'',
        //         isLiked: 'is-liked',
        //     }
        // },
        // mounted() {
        //     this.axios
        //         .get('api/post/details', {
        //             params: {
        //                 id: 'bar'
        //             }
        //         })
        //         .then(response => {
        //             this.posts = response.data.posts
        //             console.log(this.posts)
        //         })
        //         .catch(response => {
        //             console.log(response);
        //         });
        // },
    }
</script>