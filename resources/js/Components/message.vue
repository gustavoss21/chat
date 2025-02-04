<script setup>
import { request } from '../utils/request'

</script>
<script>
export default { 
    props:['user','user_received'],
    data(){
        return{
        'messages':'padrao'
        }
    },
    methods:{
        sendMessage(form){
            let headers = {
                'accept':'application/json'
            }
            let url_base = "http://127.0.0.1:8000/api/chat";
            let url = url_base+'?sender_user_id='+this.user.id+'&recipient_user_id='+this.user_received.id
            let formData = new FormData(form.target);
            request(url,'POST',formData,headers)
                .then((message)=>{
                    this.messages = message
                    console.log(message)
                }
                )

        },
    },
}
</script>

<template>
    <div>
        <form @submit.prevent="sendMessage" action="/" method="post">
    <input name="message" type="text" value="" placeholder="escreva sua mensagem">
    <input name="sender_user_id" type="hidden" :value="user.id">
    <input name="recipient_user_id" type="hidden" :value="user_received.id">
    <button type="submit">enviar</button>
</form>
{{ messages }}
    </div>
</template>
