<template>
    <div class="conversation">
        <h5 class="py-1" v-if="contact">{{ '@' + contact.username }}</h5>
        <MessagesFeed :contact="contact" :messages="messages"/>
        <MessageComposer @send="sendMessage"/>
    </div>
</template>

<script>
    import MessagesFeed from './MessagesFeed';
    import MessageComposer from './MessageComposer';
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
            }
        },
        props: {
            contact: {
                type: Object,
                default: null
            },
            messages: {
                type: Array,
                default: []
            }
        },
        methods: {
            sendMessage(text) {
                if (!this.contact) {
                    return;
                }
                axios.post('api/messages/send', {
                    contact_id: this.contact.id,
                    text: text
                }, {
                    'headers' : {
                        'Accept' : 'application/json',
                        'Authorization' : 'Bearer '+this.$store.getters.token,
                    }
                }).then((response) => {
                    this.$emit('new', response.data);
                })
            }
        },
        components: {MessagesFeed, MessageComposer}
    }
</script>