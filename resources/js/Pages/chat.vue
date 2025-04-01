<script type="setup">
import { computed, defineComponent, toRaw } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { request } from '@/utils/request';
import { Message } from '../utils/Message';
import ListContact from './listContact.vue';

export default {
    props:['user','token'],
    data(){
        return{
            users:[],
            online_user : 1,
            offline_user : 0,
            componets:
                {
                    'listContact':{'name':'listContact','user':this.user,'token':this.token},
                    'chatMessage':{'name':'chatMessage','user':this.user,'token':this.token,'user_received':null,'new_message':null},
                    'contactUs':{'name':'contactUs','user':this.user,'token':this.token},
                    'ContactUsMain':{'name':'ContactUsMain','user':this.user,'token':this.token}
                },
            componete_active:{},
            classMessage: new Message(this.user.id),

        }
    },
    methods:{
        setComponent(component,data){
            if(!Object.hasOwn(this.componets,component))return;
        
            this.componete_active = this.componets[component]
            this.componete_active['classMessage'] = this.classMessage

            if(data){
                Object.keys(data).forEach(key=>{
                    if(Object.hasOwn(this.componete_active,key)){
                        this.componete_active[key] = data[key];
                    }
                })
            }

        },
        getContacts(){
            let user_name = this.user['name'];

            let users = request('http://127.0.0.1:8000/api/users/?name='+user_name)
            .then(response=>{this.users = response,this.componete_active['users']= response});
        },
        setStatusUsers(users,status){
            users.forEach((user) => {
                let user_current = this.users.find(user_search=>user.id===user_search.id)
                if(user_current){
                    user_current['status_user'] = status
                }
            });
        }
    },
    mounted(){
        this.getContacts()
        this.setComponent('listContact')
        //ouve quando a mensagem for recebida
        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.setMessage(e);
            })

        Echo.join('user.status.transmition')
        .here((users) => {
            this.setStatusUsers(users,this.online_user);
        })
        .joining((user) => {
            this.setStatusUsers([user],this.online_user);
        })
        .leaving((user_leaving) => {
            this.setStatusUsers([user_leaving],this.offline_user)
        })
        .error((error) => {
            console.log('esta error')

            console.error(error);
        }) 
    }

}
</script>

<template>
    <!-- <componete_active   ></componete_active> -->
    <component :is="componete_active['name']", v-bind="componete_active" :setComponent="setComponent"></component>
</template>
