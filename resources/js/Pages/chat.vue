<script type="setup">
import { computed, defineComponent, toRaw } from 'vue'
import { Head, Link } from '@inertiajs/vue3';
import { request } from '../utils/request'

// import {Echo} from '../echo.js';


export default {
    props:['user','user_received','new_message','token'],
    data() {
        return{
            url : "http://127.0.0.1:8000/chat",
            messages:[],
            message:'',
            status_options:{'error':-1,'pending':0,'success':1,'viewed':2},
            status_message:0,
            host: location.host
        }
    },
    methods:{
        async getmessages(){
            let headers = {
                'accept':'application/json'
            }

            let user_name = this.user['name'];
            this.messages = await request('api/chat?sender_user_id='+this.user.id+'&recipient_user_id='+this.user_received.id)

            return this.messages
        },

        callUpdateMessage(){
            let messages_viewed = this.messages.filter(message=>(this.user.id === message.recipient_user_id && message.status !== this.status_options['viewed']))
            if(!messages_viewed.length)return;

            this.updateMessageView(messages_viewed)
        },

        updateMessageView(messages){
            let method = {'_method':'PATCH'}
            let url = 'api/chat'

            messages.push(method);
            let body = JSON.stringify(messages);

            let headers = {
                "Content-Type": "application/json"
            }

            request(url,'PATCH',body,headers)
                .then(response=>console.log(response))
                .catch(error=>console.error(error))
        },

        addMessage(form,msg){
            let message = {
                id:0,
                sender_user_id:this.user.id,
                message:msg,
                status:this.status_options['pending']

            }

            this.messages.push(message);

            this.sendMessage(form)

            msg = '';
        },

        sendMessage(form){
            let headers = {
                'accept':'application/json'
            }
            let url_base = "http://127.0.0.1:8000/api/chat";
            let url = url_base+'?sender_user_id='+this.user.id+'&recipient_user_id='+this.user_received.id
            let formData = new FormData(form.target);

            request(url,'POST',formData,headers)
                .then((msg)=>{
                    this.messages[this.messages.length -1] = msg;
                }
                ).catch((error)=>this.updateMessageStatusTemple('error'))
        },

        /**
         * updates the messages status
         * @param Number status
         * @param Number ?index
         */
         updateMessageStatusTemple(status, index=null){
            let message = {};

            if(!index){
                message = this.messages[this.messages.length - 1]
            }else{
                message = this.messages[index];
            }
            
            if(!this.status_options.hasOwnProperty(status))throw new TypeError('status error');

            message.status = this.status_options[status]

        }
    },

    mounted(){
        this.getmessages()
            .then(response=>{this.callUpdateMessage(response)});

        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.messages.push(e.message);
                this.updateMessageView([e.message])
            });


        Echo.private(`message.viewed.user.${this.user.id}`)
        .listen('MessageViewed', (e) => { 

            this.messages.forEach((element,index) => {
                if(e.messages_id.indexOf(element.id) !== -1){
                    this.updateMessageStatusTemple('viewed',index)
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
    <Message @new_message="addMessage" :user="user" :user_received="user_received" :token="token" :message="message"/>
</template>
