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
                    'listContact':{'name':'listContact','user':this.user,'users':[],'token':this.token, 'last_messages':[]},
                    'chatMessage':{'name':'chatMessage','user':this.user,'token':this.token,'user_received':null,'new_message':null},
                    'contactUs':{'name':'contactUs','user':this.user,'token':this.token},
                    'ContactUsMain':{'name':'ContactUsMain','user':this.user,'token':this.token}
                },
            componete_active:{},
            classMessage: new Message(this.user.id),
            last_messages: []

        }
    },
    methods:{
        setComponent(component,data){
            if(!Object.hasOwn(this.componets,component))return;
        
            this.componete_active = this.componets[component]
            this.componete_active['classMessage'] = new Message(this.user.id)

            if(data){
                this.setComponentData(component, data)
            }

        },
        setComponentData(component,data){

            Object.keys(data).forEach(key=>{
                    if(Object.hasOwn(this.componets[component],key)){
                        if(this.componets[component]['name'] === this.componete_active['name']){
                            this.componete_active[key] = data[key];
                        }
                        this.componets[component][key] = data[key];

                    }
                })
        },

        setContactsDatas(datas){
            this.users = datas[0]
            this.setComponentData('listContact',{'users': this.users,'last_messages': this.classMessage.setMessages(datas[1])})
        },
        getContacts(){
            let user_name = this.user['name'];

            request('http://127.0.0.1:8000/api/users/?name='+user_name)
            .then(response=>this.setContactsDatas(response))
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
        this.setComponent('listContact')
        //ouve quando a mensagem for recebida
        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.setMessages(e);
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
    },
    beforeMount(){
        this.getContacts()
    }

}
</script>

<template>
    <component :is="componete_active['name']", v-bind="componete_active" :setComponent="setComponent"></component>
</template>
