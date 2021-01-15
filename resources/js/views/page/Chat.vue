<template>
    <chat-app :user="user"></chat-app>
</template>
<script>
    export default {
        metaInfo () {
            return {
                title: 'ʟ ᴏ ɴ ᴇ ʟ ʏ',
            }
        },
        data() {
            return {
                user:this.$store.getters.getUser,
            }
        }
    }
</script>