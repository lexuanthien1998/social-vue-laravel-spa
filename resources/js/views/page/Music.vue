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
        },
        methods: {
        }
    }
</script>