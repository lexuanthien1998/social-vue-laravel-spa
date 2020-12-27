<template>
    <div>
        <div class="img-main shadow" v-bind:style="{backgroundImage: ImageMain()}">
            <div class="d-flex box-btn">
                <i class="fab fa-facebook-messenger"><span>Messenger</span></i>
                <i class="fas fa-user-plus"><span>Follow</span></i>
                <!-- <i class="fas fa-user-minus"></i> -->
                <i class="fas fa-feather" data-bs-toggle="modal" data-bs-target="#editProfile"><span>Edit profile</span></i>
            </div>
        </div>
        <div class="img-profile">
            <div class="rounded-circle shadow border border-5" v-bind:style="{backgroundImage: ImageProfile()}"></div>
        </div>
        <div class="text-center box-name">
            <p>{{user.name}}</p>
        </div>

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
                            <div class="img-main shadow" v-bind:style="{backgroundImage: ImageMain()}" style="max-height:150px;">
                                <label for="image-main">
                                    <div class="d-flex box-btn box-camera"><i class="fas fa-camera icon-camera"></i></div>
                                </label>
                                <input id="image-main" v-on:change="mainImageChange" type="file" hidden/>
                            </div>

                            <label for="image-profile" class="image-profile">
                                <div class="rounded-circle shadow border border-5" v-bind:style="{backgroundImage: ImageProfile()}"></div>
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
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
                info: {},
                users: {
                    email: '',
                    username: '',
                    name: '',
                    gender: '',
                    address: '',
                    description: '',
                    phone_number: '',
                    birth_date: '',
                    image_main: '',
                    image_profile: ''
                },
                message: '',
                image_main: "url('/images/bg.jpg')",
                image_profile: "url('/images/avatar.jpg')",

                gender: [
                    { name: 'male', value: '1' },
                    { name: 'female', value: '2' },
                    { name: 'null (private)', value: '0' },
                ],
                date: new Date().toISOString().substr(0, 10),
                menu: false,
                modal: false,
            }
        },
        mounted() {
            axios
            .get('/api/user/'+this.$route.params.username)
            .then(response => {
                this.info = response.data
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
                this.date = response.data.user.birth_date != null ? response.data.user.birth_date : this.date
                console.log(this.users);
            })
            .catch(error => {
                this.$router.back()
            });
        },
        methods: {
            ImageMain() {
                if(this.users.image_main != "") {
                    this.image_main = `url('` + this.users.image_main + `')`
                }
                return this.image_main;
            },
            ImageProfile() {
                if(this.users.image_profile != "") {
                    this.image_profile = `url('` + this.users.image_profile + `')`
                }
                return this.image_profile;
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
                    this.$router.go({ name: 'home' })
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
            }
        }
    }
</script>