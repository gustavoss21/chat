<script>
import { computed, defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { request } from '../utils/request'
import { Message } from '../utils/Message'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { stringify } from 'postcss';

export default {
    props:['user','token'],
    data() {
        return{
            url : "http://127.0.0.1:8000/chat",
            status:'aguarde alguem conectar...',
            messages:[],
            connection_established:false,
            user_id_connected : null,
            classMessage : new Message(this.user.id,this.user_id_connected)

        }
    },
    methods:{
        activeCall(user_id){
            if(user_id === this.user.id)return;
            
            this.connection_established = true;
            this.user_id_connected = user_id;
            this.classMessage.user_received_id = user_id;
        },
        deactiveCall(){
            this.connection_established = false;
            this.user_id_connected = undefined;
            this.classMessage.user_received_id = undefined;

        },

        dropCall(user){
            Echo.leave(`contact.us.${user.id}`);
            this.deactiveCall()
        }
        
    },

    mounted(){
        // this.connetContactMain();

        Echo.join(`contact.us.${this.user.id}`)
        .here((users) => {
            console.log('esta aqui')
            console.log(users);
            this.activeCall(users[0].id)
        })
        .joining((user) => {
            console.log('se juntou')
            console.log(user);
            this.activeCall(user.id)
            
        })
        
        .leaving((user) => {
            console.log('deijou')
            console.log(user);
            this.dropCall(user)
        })
        .error((error) => {
            console.log('esta error')

            console.error(error);
        }) 

        Echo.join('user.access.contact.us')

        //defined message
        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.messages.push(e.message);
                // this.message.updateMessageView([e.message])
            });
    },
}
</script>

<template>
    <Head title="contate nos" />
    <h2>{{ status }}</h2>
    <template v-if="connection_established">
        <div id="chat">
            <div v-for="message_data in classMessage.messages"> 

                <div v-if="user.id==message_data.sender_user_id" class="user_send">
                    {{ message_data.message }}   ->  {{ message_data.status }}
                </div>
                <div v-else class="user_received">
                    {{ message_data.message }}
                </div>
            </div>
            <Message @new_message="(event)=>classMessage.addMessage(...event)" :user="user" :user_id_received="user_id_connected" :token="token"/>
        </div>
    </template>
</template>
