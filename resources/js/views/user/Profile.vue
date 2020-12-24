<template>
    <div>
        <div class="img-main shadow" style="background-image: url('/images/bg.jpg');">
            <div class="d-flex box-btn">
                <div class="rounded-pill shadow">
                    <i class="fab fa-facebook-messenger"><span>Messenger</span></i>
                </div>
                <div class="rounded-pill shadow">
                    <i class="fas fa-user-plus"><span>Follow</span></i>
                    <!-- <i class="fas fa-user-minus"></i> -->
                </div>
                <div class="rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#editProfile">
                    <i class="fas fa-feather"><span>Edit profile</span></i>
                </div>
            </div>
        </div>
        <figure class="img-profile shadow">
            <img src="/images/avatar.jpg" class="img-fluid rounded-circle mx-auto" style="max-height:150px;">
        </figure>
        <div class="d-flex justify-content-center box-list-profile">
            <ul class="list-unstyled d-flex">
                <li>Posts</li>
                <li>Photos</li>
                <li>Intro</li>
                <li>Followers</li>
                <li>Following</li>
            </ul>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="editProfile" ref="hiddenmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content px-3">
                    <div class="modal-header pb-0 d-flex justify-content-end">
                        <i class="fas fa-times" data-bs-dismiss="modal"></i>
                    </div>
                    <form @submit.prevent="updateProfile" class="form-post p-3" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-signature"></i>
                                <input type="text" v-if="info.user.name" :value="info.user.name"  placeholder="your name." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-signature"></i>
                                <input type="text" v-model="info.user.username" placeholder="your username." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-signature"></i>
                                <input type="text" v-model="info.user.email" placeholder="your email." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-signature"></i>
                                <input type="text" v-model="info.user.phone_number" placeholder="your phone number." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-signature"></i>
                                <input type="text" v-model="info.user.address" placeholder="your address." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-signature"></i>
                                <input type="text" v-model="info.user.description" placeholder="description about you." class="rounded-pill px-3 py-1 ml-3 w-100">
                            </div>
                            <div class="d-flex align-items-center div-input">
                                <i class="fas fa-signature"></i>
                                <div class="ml-3 d-flex">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" v-model="info.user.sex" name="gender" id="male" :checked="info.user.sex == 0">
                                        <label class="form-check-label" for="male">
                                            male
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" v-model="info.user.sex" name="gender" id="female" :checked="info.user.sex == 1">
                                        <label class="form-check-label" for="female">
                                            female
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" v-model="info.user.sex" name="gender" id="null" :checked="info.user.sex == ''">
                                        <label class="form-check-label" for="null">
                                            null (private)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer px-0 d-flex justify-content-between">
                            <div class="image-upload">
                                <label for="file-input-modal"><i class="far fa-images"></i></label>
                                <input id="file-input-modal" v-on:change="onImageChange" type="file"/>
                            </div>
                            <button type="submit" class="btn rounded-pill py-2 px-4">Save</button>
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
                info: {}
            }
        },
        mounted() {
            axios
            .get('/api/profile/'+this.$route.params.username)
            .then(response => {
                console.log(response.data)
                this.info = response.data
            })
            .catch(error => {
                console.log(error)
                // this.$router.back()
            });
        },
        methods: {
            onImageChange(e){
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
        },
        filters: {
            check(value) {
                return value;
                // if(value) {
                //     if(value.length >= 250) {
                //         return value.substr(0,value.indexOf(' ')+limit) + '...';
                //     } else {
                //         return value;
                //     }
                // }
            }
        }
    }

    function resize(base64Str) {
        var img = new Image();
        img.src = base64Str;
        var canvas = document.createElement('canvas');
        var width = img.width;
        var height = img.height;
        var MAX_WIDTH = 720;
        var MAX_HEIGHT = Math.round(height * MAX_WIDTH / width);

        if (width > height) {
            if (width > MAX_WIDTH) {
            height *= MAX_WIDTH / width;
            width = MAX_WIDTH;
            }
        } else {
            if (height > MAX_HEIGHT) {
            width *= MAX_HEIGHT / height;
            height = MAX_HEIGHT;
            }
        }
        canvas.width = width;
        canvas.height = height;
        var ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, width, height);
        return canvas.toDataURL();
    }
</script>