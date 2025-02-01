<script>
import { computed, defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3';


export default {
    props:['user','user_received'],
    data() {
        return{
            url : "http://127.0.0.1:8000/chat",
            messages:{}
        }
    },
    methods:{
        async getmessages(){
            let headers = {
                'accept':'application/json'
            }

            let user_name = this.user['name'];

            this.messages = await this.request('http://127.0.0.1:8000/api/chat?sender_user_id='+this.user.id+'&recipient_user_id='+this.user_received.id)
            console.log(this.messages)
        },

        async request(url,method='GET',body=null,heads={}){
            let myInit = {
                method: method,
                headers: heads,
            };

            if(method === 'POST'){
                if(!body) return console.error('o paramentro body, nao foi enviado!')
                myInit['body'] = body
            }
           
            let velue_return =  await fetch(url,myInit)
            .then(response => {
                if (!response.ok) {
                  console.log("Network response was not ok.");
                  
                }
                
               return response.json();
                        },
            ).then(response=>{
            
                this.messages = response;
                return response
            })
            .catch(error => console.error(error));

            return velue_return;
        },
    },

    mounted(){
        this.getmessages()
    }

    
}
</script>

<template>
    <Head title="lista de contatos" />
    <h1>chegamos no chat</h1>
    <div v-for="message_data in messages"> 

        <div v-if="user.id==message_data.sender_user_id" class="user_send">
            {{ message_data.message }}
        </div>
        <div v-else class="user_received">
            {{ message_data.message }}
        </div>

    </div>
</template>
