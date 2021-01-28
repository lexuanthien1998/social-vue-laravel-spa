<template>
    <div class="d-flex h-100 shadow-sm messages">
        <div class="col-3 box-info">
            <div class="d-flex justify-content-center justify-content-sm-start align-items-center" v-for="(item, index) in contacts" :key="index" v-on:click="selectMessages(index)" v-bind:class="[item.id == contact.id ? 'active' : '']">
                <img :src="item.image_profile != null ? `../storage/images/users/`+ item.id + `/image_profile/` + item.image_profile : `/images/user.png`" class="img-fluid mx-2">
                <div class="d-none d-sm-block align-items-center">
                    <p>{{ item.username }} <span v-if="item.unread > 0" class="unread">{{`(` + item.unread + `)`}}</span></p>
                    <p>{{ item.name }}</p>
                </div>
            </div>
        </div>
        <div class="col mx-3 box-chat">
            <div class="d-flex align-items-center pb-2">
                <img :src="contact.image_profile != null ? `../storage/images/users/`+ contact.id + `/image_profile/` + contact.image_profile : `/images/user.png`" class="rounded-circle img-fluid" style="height:50px; width:50px">
                <p class="m-0 px-3">{{ contact.username }}</p>
            </div>
            <div class="py-2 overflow-scroll message" ref="feed">
                <div class="py-2 my-2" v-for="(message, index) in messages" :key="index" v-bind:class="[message.from == user.id ? 'text-right' : '']">
                    <span>{{message.text}}</span>
                </div>
            </div>
            <input class="row p-3 mb-3 rounded-pill" v-model="message" @keydown.enter="send" placeholder="Message...">
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                user: this.$store.getters.getUser,
                token: this.$store.getters.token,
                messages: [],
                contacts: [],
                message: '',
                contact: {},
            };
        },
        mounted() {
            Echo.private(`messages`)
                .listen('sendNewNessage', (e) => {
                    this.isSendRealtime(e.message);
                });
    
            this.axios
            .get('/api/contacts', {
                'headers' : {
                    'Accept' : 'application/json',
                    'Authorization' : 'Bearer ' + this.token,
                } 
            })
            .then((response) => {
                this.contacts = response.data;
                this.contact = this.contacts.slice(0,1);
                this.selectMessages(0) 
            });
        },
        watch: {
            messages(messages) {
                this.scrollToBottom();
            }
        },
        methods: {
            selectMessages(index) {
                axios
                .get(`/api/get-messages-for/` + this.contacts[index].id, {
                    'headers' : {
                        'Accept' : 'application/json',
                        'Authorization' : 'Bearer '+ this.token,
                    }
                })
                .then((response) => {
                    this.messages = '';
                    this.messages = response.data.filter(item => item.text !== null);
                    this.contacts[index].unread = 0;
                    this.contact = this.contacts[index];
                })
            },
            scrollToBottom() {
                setTimeout(() => {
                    this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
                }, 50);
            },
            send(e) {
                e.preventDefault();
                if (this.message == '' || Object.keys(this.contact).length == 0) {
                    return;
                }
                axios.post('api/messages/send', {
                    contact_id: this.contact.id,
                    text: this.message
                }, {
                    'headers' : {
                        'Accept' : 'application/json',
                        'Authorization' : 'Bearer ' + this.token,
                    }
                }).then((response) => {
                    this.messages.push(response.data)
                    this.message = ''
                })
            },
            isSendRealtime(message) {
                if (Object.keys(this.contact).length != 0 && message.from == this.contact.id) {
                    this.messages.push(message);
                    return;
                }
                this.updateUnreadCount(message.from_contact, false);
                //message.from_contact là gì ko biết. tự sinh ra. là thông tin của người bên kia chat vs mình
            },
            updateUnreadCount(contact, reset) {
                this.contacts = this.contacts.map((single) => {
                    if (single.id !== contact.id) {
                        return single;
                    }
                    if (reset)
                        single.unread = 0;
                    else
                        single.unread += 1;
                    return single;
                })
            }
        },
    }
</script>
