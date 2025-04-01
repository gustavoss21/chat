<script>
import { computed, defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { request } from '../utils/request'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    props:['user','users','token','setComponent'],
    data() {
        return{
            
            url : "http://127.0.0.1:8000/chat",
            status_user:'offline',

        }
    },
    methods:{
        /**
         * @param string status <br>
         * @description options status list => ['offline','online']
         */
        statusUser(status='offline') {
            let headers = {
                'content-type': 'application/json',
            };
            // heads.Origin = location.origin;
            let body = 
                {
                    'user_id': this.user.id,
                    'status_user':status,
                    'tramission':false,
                    'user_received_id':0,
                    '_method':'POST',
                    '_token':this.token
                };

            // request('http://127.0.0.1:8000/status-user','POST',body,headers)
            //     .then(response=>console.log(response))
            //     .catch(error=>console.error(error));
            
            
            headers['X-CSRF-TOKEN'] = document.querySelector('meta[name=csrf-token]').content
            headers.Accept = 'application/json';
            headers.Origin = location.origin;

            let myInit = {
                body:body,
                headers:headers

            }

            let myInits = new Blob([JSON.stringify(body)],{type: 'application/json'})
            navigator.sendBeacon('http://127.0.0.1:8000/api/status-user',myInits);
        },


    },
}
</script>

<template>
    <Head title="lista de contatos" />
    <NavBar :user="user" :token="token"></NavBar>
    <h1>chegamos</h1>
    <a @click.prevent="(e)=>setComponent('chatMessage',{'user_received':user_data})"  v-for="user_data in users" :href="url+'?send='+user.id+'&received='+user_data.id"> 
        <div class="contact">
            {{ user_data.name }} <b>{{ user_data.status_user }}</b>
        </div>
    </a>
</template>
