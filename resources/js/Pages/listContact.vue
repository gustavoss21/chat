<script>
import { computed, defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    props:['user','users','token','setComponent','last_messages'],
    data() {
        return{
            
            url : "http://127.0.0.1:8000/chat",
            status_user:'offline',
            status:{
                0:'offline',
                1:'online'
            },
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
    <div id="contacts">
            <div class="contact" v-for="(user_data,key) in users" :key="key" >
                <a @click.prevent="(e)=>setComponent('chatMessage',{'user_received':user_data})"  :href="url+'?send='+user.id+'&received='+user_data.id"> 
                    <div class="icon-contact">
                        <span class="icon-status" :id="'icon-status-'+status[user_data.status_user]"></span>
                    </div>
                    <div>
                        {{ user_data.name }}
                    </div>
                </a>
                <template v-if="last_messages[key]">

                    <div class="last-message light-gray-color">{{ last_messages[key].message }}</div>
                
                    <span class="list-item-time-top light-gray-color">{{last_messages[key].time}}</span>
                </template>
            </div>
            <div class="division-line light-gray-color"></div>
    </div>
  
</template>
