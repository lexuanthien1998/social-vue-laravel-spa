<template>
    <ul class="list-unstyled box-messages">
        <li v-for="contact in contacts" :key="contact.id" class="rounded-pill p-2" @click="selectContact(contact)" :class="{ 'selected': contact == selected }">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <img :src="contact.image_profile != null ? `../storage/images/users/`+ contact.id + `/image_profile/` + contact.image_profile : `/images/user.png`" style="width:35px; height:35px; vertical-align: middle;" class="rounded-circle mr-2 img-fluid">
                    <p class="username">{{ contact.username }}</p>
                </div>
                <span class="rounded-circle unread" v-if="contact.unread"><i class="fas fa-bell"></i></span>
            </div>
        </li>
    </ul>
</template>

<script>
    export default {
        props: {
            contacts: {
                type: Array,
                default: [],
                required: true
            }
        },
        data() {
            return {
                selected: this.contacts.length ? this.contacts[0] : null,
            };
        },
        methods: {
            selectContact(contact) {
                this.selected = contact;
                this.$emit('selected', contact);
            }
        }
    }
</script>