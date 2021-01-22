<template>
    <div class="d-flex h-100">
        <ContactsList class="col-3" :contacts="contacts" @selected="startConversationWith"/>
        <Conversation class="col ml-5 px-8" :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
    </div>
</template>

<script>
    import Conversation from './Conversation';
    import ContactsList from './ContactsList';
    export default {
        props: {
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                selectedContact: null,
                messages: [],
                contacts: []
            };
        },
        mounted() {
            // Echo.private(`messages-${this.user.id}`)
            //     .listen('NewMessage', (e) => {
            //         this.hanleIncoming(e.message);
            //     });
    
            this.axios
            .get('/api/contacts', {
                'headers' : {
                    'Accept' : 'application/json',
                    'Authorization' : 'Bearer '+this.$store.getters.token,
                } 
            })
            .then((response) => {
                this.contacts = response.data;
            });
        },
        methods: {
            startConversationWith(contact) {
                this.updateUnreadCount(contact, true);
                axios
                .get(`/api/get-messages-for/${contact.id}`, {
                    'headers' : {
                        'Accept' : 'application/json',
                        'Authorization' : 'Bearer '+this.$store.getters.token,
                    }
                })
                .then((response) => {
                    this.messages = response.data;
                    this.selectedContact = contact;
                })
            },
            saveNewMessage(message) {
                this.messages.push(message);
            },
            hanleIncoming(message) {
                if (this.selectedContact && message.from == this.selectedContact.id) {
                    this.saveNewMessage(message);
                    return;
                }
                this.updateUnreadCount(message.from_contact, false);
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
        components: {Conversation, ContactsList}
    }
</script>
