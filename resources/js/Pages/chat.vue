<script type="setup">
import { computed, defineComponent, toRaw } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { Message } from '../utils/Message';
import { request } from '@/utils/request';
import '../../css/chat.css';
// import {Echo} from '../echo.js';

export default {
    props:['user','user_received','new_message','token'],
    data() {
        return{
            url : "http://127.0.0.1:8000/chat",
            classMessage: new Message(this.user.id,this.user_received.id),
            host: location.host,
            status:{
                'online':1,
                'offline':0
            },
            messages:undefined,
            messageDay:undefined,
            status_user:0,
            message_date:''
        }
    },
    methods:{
        setStatusUser(status){
            if(!this.status.hasOwnProperty(status))return;

            this.status_user = this.status[status]
        },
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

        setAndReturnData(data){
            this.message_date = data
            return data;
        }
    },
    mounted(){
        //ouve quando a mensagem for enviado
        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.setMessage([e.message]);
                this.classMessage.updateMessageView([e.message])
            })

        //ouve quando a mensagem for visualuzada
        Echo.private(`message.viewed.user.${this.user.id}`)
        .listen('MessageViewed', (e) => { 

            this.classMessage.messages.forEach((element,index) => {
                if(e.messages_id.indexOf(element.id) !== -1){
                    this.classMessage.updateMessageStatusTemple(Message.status_viewed,index)
                }
            });
        });

        Echo.join('user.status.transmition')
        .here((users) => {

            let user_online = users.find(
                    search =>search.id == this.user_received.id
                )

            if(!user_online)return;

            this.setStatusUser('online')
        })
        .joining((user) => {
            // this.statusUser('online')
            let user_online = user.id == this.user_received.id
            
            if(!user_online)return;

            this.setStatusUser('online')
            
        })
        .leaving((user_leaving) => {
            this.setStatusUser('offline')

        })
        .error((error) => {
            console.log('esta error')

            console.error(error);
        }) 
    },
    beforeMount(){
        this.classMessage.getmessages(this.user_received)
        .then(response=>{this.classMessage.callUpdateMessage(response);this.classMessage.setMessage(response)});
    }
}
</script>

<template>
    <Head title="conversa" />
    <Message @new_message="(data)=>classMessage.addMessage(...data)" title="conversar" :user="user" :user_received="user_received" :token="token" :status_user="status_user"/>
    <div id="content-message">
        <template v-for="message_data in classMessage.messages"> 
            <div class="date" v-if="message_data.created_at">
                {{ message_data.created_at }}
            </div>
            <div v-if="user.id==message_data.sender_user_id" class="user_send message-item">
                {{ message_data.message }}
            <div class="time-user-current">
                <span>{{ message_data.time }}</span>
                <span class="icon-message">
                    <div :class="message_data.status_class+ ' icon-message-bar icon-message-bar-one'"></div>
                    <div :class="message_data.status_class+ ' icon-message-bar icon-message-bar-two'"></div>
                    <div v-if="message_data.status == 2" :class="message_data.status_class+ ' icon-message-bar icon-message-bar-tree'"></div>
                </span>
            </div>

            </div>
            <div v-else class="user_received message-item">
                {{ message_data.message }}
                <div class="time">
                    <span>{{ message_data.time }}</span>
                </div>

            </div>

        </template>
    </div>
</template>
