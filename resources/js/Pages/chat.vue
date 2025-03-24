<script type="setup">
import { computed, defineComponent, toRaw } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { Message } from '../utils/Message';
import { request } from '@/utils/request';
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

            status_user:0


        }
    },
    methods:{
        setStatusUser(status){
            if(!this.status.hasOwnProperty(status))return;

            this.status_user = this.status[status]
        },
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
    },
    mounted(){
        console.log('userReceivid')
        console.log(this.user_received)
        // window.addEventListener("beforeunload", ()=>this.statusUser('online'));
        
        // this.statusUser('online')

        this.classMessage.getmessages(this.user_received)
            .then(response=>{this.classMessage.callUpdateMessage(response)});

        // Echo.private(`chat.${this.user.id}`)
        //     .listen('SendMessage', (e) => {
        //         this.classMessage.messages.push(e.message);
        //         this.classMessage.updateMessageView([e.message])
        //     })
        //     .here(users => {
        //         console.log("Usuários online:", users);
        //     })
        //     .joining(user => {
        //         console.log(user.name + " entrou no chat");
        //     })
        //     .leaving(user => {
        //         console.log(user.name + " saiu do chat");
        //         // Atualizar status do usuário no Vue
        //     });


        Echo.private(`message.viewed.user.${this.user.id}`)
        .listen('MessageViewed', (e) => { 

            this.classMessage.messages.forEach((element,index) => {
                if(e.messages_id.indexOf(element.id) !== -1){
                    this.classMessage.updateMessageStatusTemple('viewed',index)
                }
            });
        });

        Echo.join('user.status.transmition')
        .here((users) => {
            console.log('esta aqui')
            console.log(users);
            let user_online = users.find(
                    search =>search.id == this.user_received.id
                )

            if(!user_online)return;

            this.setStatusUser('online')
        })
        .joining((user) => {
            console.log('se juntou')
            console.log(user);
            // this.statusUser('online')
            let user_online = this.users.find(user=>user.id == user.id)
            
            if(!user_online)return;

            this.setStatusUser('online')
            
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
        // .listen('SendStatusUser', (e) => {
        //     console.log('eventoenviado');
        //     console.log(e);

        //     let user_online = this.users.find(user=>user.id == e.user_status_id)
            
        //     if(!user_online)return;

        //     user_online.status_user = 1
            
        // });

    },
    beforeUnmount() {
        // window.removeEventListener("beforeunload", this.statusUser);
  },
    computed:{

    }
}
</script>

<template>
    <Head title="lista de contatos" />
    <h1>chegamos no chat</h1>
    <div id="user-received">
        <h2>{{ user_received.name }}</h2>
        <span>{{ status_user }}</span>
    </div>
    <div v-for="message_data in classMessage.messages"> 

        <div v-if="user.id==message_data.sender_user_id" class="user_send">
            {{ message_data.message }}   ->  {{ message_data.status }}
        </div>
        <div v-else class="user_received">
            {{ message_data.message }}
        </div>
    </div>
    <Message @new_message="(data)=>classMessage.addMessage(...data)" :user="user" :user_id_received="user_received.id" :token="token"/>
</template>
