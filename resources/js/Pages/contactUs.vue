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
        activeCall(){
            let headers = {
                // 'accept':'application/json',
                "Content-Type": "application/json"
            };

            let formdata = JSON.stringify(this.user);
            // return;
            console.log(formdata)

            request('http://127.0.0.1:8000/api/active-call','POST',formdata,headers)
                .then(response=>{console.log('user comum active call');console.log(response)});
                
        },
        
        execRequest(){            
            let headers = {
                    'Accept':'application/json',
                    'Content-Type':'application/json'
                }

            let body = JSON.stringify({'user_id':this.user.id,'super_user_id':this.user_id_connected})
            request('http://127.0.0.1:8000/api/user-conect','POST',body,headers)
                .then(response=>{
                    console.log('user comum confirmed');
                    this.connection_established = true;
                    console.log(this.user_id_connected)
                })
        },
        handleUserLeave() {
            navigator.sendBeacon("http://127.0.0.1:8000/api/drop-call", JSON.stringify({
                'user_id':this.user.id,
                'super_user_id':this.user_id_connected,
                'user_receive':this.user.id
            }))
        }
        
    },
    // beforeUnmount,
    // deactivated,
    // unmounted,

    mounted(){
        window.addEventListener("beforeunload", this.handleUserLeave);
        console.log(this.user)
        this.activeCall();

        Echo.private(`user.conect.${this.user.id}`)
            .listen('AlertsArrivalMainUser', (e) => {
                this.status = 'já estamos online, relate seu problema';
                this.classMessage.user_received_id = e.user_main_active_id
                this.user_id_connected = e.user_main_active_id
                console.log('us.'+this.user_id_connected)
                this.execRequest()
            });

        //defined message
        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.messages.push(e.message);
                // this.message.updateMessageView([e.message])
            });

        Echo.private(`call.closed.${this.user.id}`)
            .listen('CallClosedEvent', (e) => {
                console.log('o chamada foi encerrada')
                this.connection_established = false;
            });
    },
    beforeUnmount(){
        window.removeEventListener("beforeunload", this.handleUserLeave);
        // let body = JSON.stringify(
        //     {
        //         'user_id':this.user.id,
        //         'super_user_id':this.user_id_connected,
        //         'user_receive':this.user.id
        //     })

        // request('http://127.0.0.1:8000/api/drop-call',body,'POST')
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
            <Message @new_message="(event,msg)=>classMessage.addMessage(event,msg)" :user="user" :user_id_received="user_id_connected" :token="token"/>
        </div>
    </template>
</template>
