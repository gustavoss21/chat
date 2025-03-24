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
            console.log('chamado')
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

        getContacts(){
            let headers = {
                'accept':'application/json'
            }

            let user_name = this.user['name'];

            let rest = request('http://127.0.0.1:8000/api/users/?name='+user_name)
            .then(response=>{console.log(response);this.users = response});

            return rest
        },
    },

    mounted(){
        this.getContacts()

        Echo.join('user.status.transmition')
        .here((users) => {
            // console.log('esta aqui')
            // console.log(users);
            let user_online = this.users.filter(
                user=>users.find(
                    search =>search.id == user.id
                )
                ?true
                :false
            )

            if(!user_online.length)return;

            user_online.forEach(user=>user.status_user = 1)
        })
        .joining((user) => {
            console.log('se juntou')
            console.log(user);
            this.statusUser('online')
            let user_online = this.users.find(user=>user.id == user.id)
            
            if(!user_online)return;

            user_online.status_user = 1
            
        })
        .leaving((user) => {
            console.log('deijou')
            console.log(user);

            let user_online = this.users.find(user=>user.id == user.id)
            
            if(!user_online)return;

            user_online.status_user = 0
        })
        .error((error) => {
            console.log('esta error')

            console.error(error);
        }) 
    },
}
</script>

<template>
    <Head title="lista de contatos" />
    <NavBar :user="user" :token="token"></NavBar>
    <h1>chegamos</h1>
    <a  v-for="user_data in users" :href="url+'?send='+user.id+'&received='+user_data.id"> 
        <div class="contact">
            {{ user_data.name }} <b>{{ user_data.status_user }}</b>
        </div>
    </a>
</template>
