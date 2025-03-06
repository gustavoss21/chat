<script>
import { computed, defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { request } from '../utils/request';
import {Message} from '../utils/Message'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';


export default {
    props:['user','token'],
    data() {
        return{
            url : "http://127.0.0.1:8000/chat",
            users_for_call: [],
            connection_established:false,
            user_id_connected:{},
            classMessage:new Message(this.user.id,this.user_id_connected),
        }
    },
    methods:{
        getCallUsers(){
            let headers = {
                'accept':'application/json'
            };

            request('http://127.0.0.1:8000/api/call-users','GET',headers)
                .then(response=>{
                    this.users_for_call = response;
                    console.log(this.users_for_call)
                })
                .catch(erro=>console.error(response))
                
        },

        activateMainCall(){
            if(this.users_for_call.length < 1) return;

            let headers = {
                'accept':'application/json'
            };

            request('http://127.0.0.1:8000/api/active-main-call?user_id='+this.users_for_call[0].user_id,'GET',headers)
                .then(response=>{
                    console.log('user main entra na call');
                    this.users_for_call.shift()

                });
                
        },
        dropCall(){
            request('http://127.0.0.1:8000/api/drop-call?user_id='+this.user_id_connected,'POST',headers)
                .then(response=>{
                    // console.log('user main entra na call');

                    // this.user_id_connected = this.users_for_call.shift()
                    // this.classMessage.user_received_id = this.user_id_connected.user_id;
                    // this.connection_established = false;
                });
        }
    },

    mounted(){
        this.getCallUsers()

        Echo.private(`main.user.conect`)
            .listen('AlertArrivalUser', (e) => {
               console.log('user main recebeu o evento')
               console.log(e)
               if(this.users_for_call.find((user) => user.user_id === e.user_id))return;
                    this.users_for_call.push(e)
                });

        Echo.private(`user.confirmation.${this.user.id}`)
            .listen('UserConnectionConfirmation', (e) => {
                console.log('o usuário confirmou')
                this.connection_established = true;
                this.user_id_connected = e.user_id
                this.classMessage.user_received_id = this.user_id_connected;
            });
        
        Echo.private(`call.closed.${this.user.id}`)
            .listen('CallClosedEvent', (e) => {
                console.log('o chamada foi encerrada')
                this.connection_established = false;
            });

        //defined message
        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.messages.push(e.message);
            });
    }
}
</script>

<template>
    <Head title="contate nos" />
    <h2>Painel de controle</h2>
    <div>
        <button @click="activateMainCall" type="button">entrar no chat{{ users_for_call.length }}</button>
        <br>
        <button type="button" @click="dropCall">sair do chat</button>
    </div>
        <div id="chat">
            <div v-for="message_data in classMessage.messages"> 

                <div v-if="user.id==message_data.sender_user_id" class="user_send">
                    {{ message_data.message }}   ->  {{ message_data.status }}
                </div>
                <div v-else class="user_received">
                    {{ message_data.message }}
                </div>
            </div>
        </div>
        <template v-if="connection_established">
            <Message @new_message="(event,msg)=>classMessage.addMessage(event,msg)" :user="user" :user_id_received="user_id_connected" :token="token"/>
        </template>

</template>
