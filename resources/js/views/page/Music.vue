<template>
    <div class="h-100 music" v-if="songs.length > 0">
        <div class="d-flex shadow-sm align-items-center shadow-sm mb-2 box-content" v-for="(song, index) in songs" :key="index" @click="openSong(index)">
            <img :src="song.thumbnail" class="img-fluid" alt="">
            <div>
                <p class="m-0">{{song.name}}</p>
                <p class="m-0 artists_names">{{song.artists_names}}</p>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                songs: {},
            }
        },
        mounted() {
            this.axios
            .get('/api/music')
            .then(response => {
                console.log(response)
                return
                this.songs = response.data.data.song
                this.$store.dispatch('addSongs', response.data.data.song)
            })
            .catch(response => {
                return
            });
            // this.axios
            // .get('https://zingmp3.vn/api/song/get-song-info?id=ZO89BBBZ&ctime=160718421&sig=7553fe9db5f0debf657efe50857408a232cd86ed7b586c27e10d505f53edbde2fbfe24407ec48b7eac0cb8b6fa8ce955bf4484df55a16db7627a8de8772d6ffb&api_key=38e8643fb0dc04e8d65b99994d3dafff', {
            //     headers: {
            //     'Access-Control-Allow-Origin': 'https://zingmp3.vn',
            //     'Access-Control-Allow-Methods': 'GET, POST , OPTIONS ,PUT, DELETE, PATCH',
            //     'Access-Control-Allow-Headers': 'Accept, Origin, Content-Type, Session-Key , Api-Key',
            //     'Access-Control-Allow-Credentials': true,
            //     'Content-Type': 'text/json;charset=utf-8',
            //     'X-Server': 'ZingMp3-api-2'
            //     },
            //     mode: 'cors',
            // })
            // .then(response => {
            //     console.log(response)
            //     return
            //     this.songs = response.data.data.song
            //     this.$store.dispatch('addSongs', response.data.data.song)
            // })
            // .catch(response => {
            //     return
            // });
        },
        methods: {
        }
    }
</script>