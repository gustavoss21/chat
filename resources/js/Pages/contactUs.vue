<script>
import { computed, defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { request } from '../utils/request'
import { Message } from '../utils/Message'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { stringify } from 'postcss';
import '../../css/contact.css';

export default {
    props:['user','token'],
    data() {
        return{
            url : "http://127.0.0.1:8000/chat",
            status:'aguarde alguem conectar...',
            messages:[],
            connection_established:false,
            user_received : null,
            user_connected : {},
            classMessage : new Message(this.user.id),
        }
    },
    methods:{
        activeCall(user){
            if(user.id === this.user.id)return;

            this.connection_established = true;
            this.user_connected = user;
            this.user_connected.status_user = 1
            this.classMessage.user_received_id = user.id;
            this.status = 'Estamos conectado'
        },
        deactiveCall(){
            this.connection_established = false;
            this.user_connected = undefined;
            this.classMessage.user_received_id = undefined;
            this.status = "Chamada encerrada!\nVolte sempre que precisar"
        },

        dropCall(user){

            Echo.leave(`contact.us.${this.user.id}`);
            Echo.leave('user.access.contact.us')
            this.deactiveCall()
        }
        
    },

    mounted(){
        //evita que o dialogo encerre sem a inteção
        window.addEventListener("beforeunload", function (event) {
           event.preventDefault();
        });

        Echo.join(`contact.us.${this.user.id}`)
        .here((users) => {
            this.activeCall(users[0])
        })
        .joining((user) => {
            this.activeCall(user)
            
        })
        
        .leaving((user) => {
            this.dropCall(user)
        })
        .error((error) => {
            console.log('esta error')

            console.error(error);
        }) 

        // alerta quando o usuário esta disponivel para contato
        Echo.join('user.access.contact.us')

        //ouve quando a mensagem for recebida
        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.setMessages([e.message]);
                this.classMessage.updateMessageView([e.message])
            })
    },
    computed:{
        button_sair(){
            return  this.connection_established?'button button-closed-call':'button btn-deactive'
    },
        connected_style_id(){
            return this.connection_established?'user-active':'';
        }
}
}
</script>

<template>
    <Head title="contate nos" />
    <template v-if="connection_established">
        <div class="content-button" :id="connected_style_id">
            <button :class="button_sair" type="button" @click="dropCall(user_connected)">sair do chat</button>
        </div>
        <chatMessage :user="user" :user_received="user_connected" new_message="" :token="token" :classMessage="classMessage" has_messages="true"></chatMessage>
    </template>
    <h1 v-else>
        {{ status }}
    </h1>
</template>
