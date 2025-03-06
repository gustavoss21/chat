<script>
import { computed, defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { request } from '../utils/request'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    props:['user','token'],
    data() {
        return{
            teste:'enos',
            users:['tese','smari','sfsoefiwe','dsfs'],
            url : "http://127.0.0.1:8000/chat"
        }
    },
    methods:{
        getContacts(){
            let headers = {
                'accept':'application/json'
            }

            let user_name = this.user['name'];

            request('http://127.0.0.1:8000/api/users/?name='+user_name)
            .then(response=>this.users = response);
        },
    },

    mounted(){
        this.getContacts()
    }
}
</script>

<template>
    <Head title="lista de contatos" />
    <NavBar :user="user" :token="token"></NavBar>
    <h1>chegamos</h1>
    <a  v-for="user_data in users" :href="url+'?send='+user.id+'&received='+user_data.id"> 
        <div class="contact">
            {{ user_data.name }}
        </div>
    </a>
</template>
