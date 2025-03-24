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
            user_connected_id:undefined,
            connection : undefined,
            classMessage:new Message(this.user.id,this.user_connected_id),
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
        dropCall(user){
            this.users_for_call.forEach((user_value,index) => {
                if(user_value.id === user.id){
                    if(this.user_connected_id === user_value.id){
                        this.connection_established = false;
                    }

                    this.users_for_call.splice(index,1)
                }
            });

            Echo.leave(`contact.us.${user.id}`);
            this.classMessage.user_received_id = undefined;

        },
        callUser(){
            let user_for_Call = this.users_for_call[0]
            this.user_connected_id = user_for_Call.id;
            this.connection_established = true;

            Echo.join(`contact.us.${this.user_connected_id}`);
            this.classMessage.user_received_id = this.user_connected_id

        },
        addNewUser(user){
            if(Array.isArray(user)){
                let valid_users = user.filter((user_value)=>user_value.id !== this.user.id)
                this.users_for_call.push(...valid_users)
                return
            }

            this.users_for_call.push(user)
        },
        nextCall(){},
        /** check the connection, call the user if there is no connection */
        establishConnection(){
            
            if(this.connection_established)return;
            if(this.users_for_call.length < 1)return;
            
            this.callUser();
        }
    },

    mounted(){
        //defined message
        Echo.private(`chat.${this.user.id}`)
            .listen('SendMessage', (e) => {
                this.classMessage.messages.push(e.message);
            });


        Echo.join('user.access.contact.us')
        .listen('AlertsArrivalUser', (e) => {
                console.log(e);
            })
        .here((users) => {
            console.log('esta aqui')
            console.log(users);
            this.addNewUser(users)
        })
        .joining((user) => {
            console.log('se juntou')
            console.log(user);
            this.addNewUser(user)
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
    }

    
}
</script>

<template>
    <Head title="contate nos" />
    <h2>Painel de controle</h2>
    <div>
        <button @click="establishConnection" type="button">entrar no chat{{ users_for_call.length }}</button>
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
            <Message @new_message="(event)=>classMessage.addMessage(...event)" :user="user" :user_id_received="user_connected_id" :token="token"/>
        </template>

</template>
