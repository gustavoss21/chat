<script>
import { computed, defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { request } from '../utils/request';
import {Message} from '../utils/Message'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import '../../css/contact.css';

export default {
    props:['user','token'],
    data() {
        return{
            url : "http://127.0.0.1:8000/chat",
            users_for_call: [],
            connection_established:false,
            user_connected:undefined,
            connection : undefined,
            classMessage:new Message(this.user.id),
            user_to_close:{}
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
                    this.users_for_call.shift()

                });
                
        },
        dropCall(user){
       
            if(this.user_connected && this.user_connected.id === user.id){
                this.connection_established = false;
                this.classMessage = new Message(this.user.id),
                Echo.leave(`contact.us.${this.user_connected.id}`);

            }                
        },
        callUser(){
            let user_for_Call = this.users_for_call.shift()
            this.user_connected = user_for_Call;
            this.connection_established = true;

            Echo.join(`contact.us.${this.user_connected.id}`)
            .leaving((user) => {
                this.dropCall(user)
            })
            .error((error) => {
                console.log('esta error')

                console.error(error);
            }) ;
            this.classMessage.user_received_id = this.user_connected.id
            this.user_connected.status_user = 1

        },
        addNewUser(user){
            if(Array.isArray(user)){
                let valid_users = user.filter((user_value)=>user_value.id !== this.user.id)
                this.users_for_call.push(...valid_users)
                return
            }

            this.users_for_call.push(user)
        },
        nextCall(){
            this.dropCall(this.user_connected)
            this.callUser()
        },
        /** check the connection, call the user if there is no connection */
        establishConnection(){
            
            if(this.connection_established)return;
            if(this.users_for_call.length < 1)return;
            
            this.callUser();
        }
    },

    mounted(){
        //evita que o dialogo encerre sem a inteção
        window.addEventListener("beforeunload", function (event) {
           event.preventDefault();
        });
    
        //ouve quando a mensagem for recebida
        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.setMessages([e.message]);
                this.classMessage.updateMessageView([e.message])

            })

        // alerta quando algum usuário entrou em contato
        Echo.join('user.access.contact.us')
        .here((users) => {
            this.addNewUser(users)
        })
        .joining((user) => {
            this.addNewUser(user)
        })   
        .leaving((user) => {
            this.dropCall(user)
        })
        .error((error) => {
            console.log('esta error')

            console.error(error);
        }) 
    },

    computed:{
        button_entrar(){
            if(this.users_for_call.length<1)return 'button btn-deactive'
            
            return 'button button-init-call'
        },

        button_sair(){
            return  this.users_for_call.length>=1 && this.connection_established?'button button-closed-call':'button btn-deactive'
    },
        userCount(){
            return  this.users_for_call.length>=1?true:false
    },
        connected_style_id(){
            return this.connection_established?'user-active':'';
        },
}

    
}
</script>

<template>
    <Head title="contate nos" />
    <h1>Painel de controle</h1>
    <div class="content-button" :id="connected_style_id">
        <button v-if="!connection_established" :class="button_entrar" @click="establishConnection" type="button">
            entrar no chat
            <span v-if="userCount" id="number-user">
                {{ users_for_call.length }}
            </span>
        </button>
        <button v-else="connection_established" :class="button_entrar" @click="nextCall" type="button">
            proxima call >
            <span v-if="userCount" id="number-user">
                {{ users_for_call.length }}
            </span>
        </button>
        <br>
        <button :class="button_sair" type="button" @click="dropCall(user_connected)">sair do chat</button>
    </div>
        <template v-if="connection_established">
            <chatMessage :user="user" :user_received="user_connected" new_message="" :token="token" :classMessage="classMessage" has_messages="true"></chatMessage>        
        </template>

</template>
