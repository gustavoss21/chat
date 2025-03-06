<script type="setup">
import { computed, defineComponent, toRaw } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { Message } from '../utils/Message';
// import {Echo} from '../echo.js';


export default {
    props:['user','user_received','new_message','token'],
    data() {
        return{
            url : "http://127.0.0.1:8000/chat",
            classMessage: new Message(this.user.id,this.user_received.id),
            host: location.host

        }
    },
    mounted(){
        this.classMessage.getmessages(this.user_received)
            .then(response=>{this.classMessage.callUpdateMessage(response)});

        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.messages.push(e.message);
                this.classMessage.updateMessageView([e.message])
            });


        Echo.private(`message.viewed.user.${this.user.id}`)
        .listen('MessageViewed', (e) => { 

            this.classMessage.messages.forEach((element,index) => {
                if(e.messages_id.indexOf(element.id) !== -1){
                    this.classMessage.updateMessageStatusTemple('viewed',index)
                }
            });
        });

    },
}
</script>

<template>
    <Head title="lista de contatos" />
    <h1>chegamos no chat</h1>
    <div v-for="message_data in messages"> 

        <div v-if="user.id==message_data.sender_user_id" class="user_send">
            {{ message_data.message }}   ->  {{ message_data.status }}
        </div>
        <div v-else class="user_received">
            {{ message_data.message }}
        </div>
    </div>
    <Message @new_message="(event)=>classMessage.addMessage" :user="user" :user_received="user_received" :token="token"/>
</template>
