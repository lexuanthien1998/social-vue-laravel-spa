<template>
    <div class="input-send-messages">
        <input class="rounded-pill" v-model="message" @keydown.enter="send" placeholder="Message...">
    </div>
</template>

<script>
    export default {
        data() {
            return {
                message: ''
            };
        },
        methods: {
            send(e) {
                e.preventDefault();
                if (this.message == '') {
                    return;
                }
                this.$emit('send', this.message);
                this.message = '';
            }
        }
    }
</script>