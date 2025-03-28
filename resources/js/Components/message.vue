<script setup>
import { request } from '../utils/request'

</script>
<script>
export default { 
    props:['user','user_received','token','title','status_user'],
    data(){
        return {
            message:'',
            status:{
                0:'offline',
                1:'online'
            },
        }
    },

    methods:{
        emitForm($event){
            let data = [$event,this.message]
            this.$emit('new_message',data);
            this.message = ''
        }


    },
}
</script>

<template>
    <div>
        <h1>{{ title }}</h1>
        <div id="user_received">
            <h2>{{ user_received.name }}</h2>
            <span class="icon-status" :id="'icon-status-'+status[status_user]"></span>
            <span id="status">{{ status[status_user] }}</span>
        </div>

        <form @submit.prevent="emitForm" action="/" method="post">
            <input type="hidden" name="_token" :value="token" autocomplete="off">
            <input name="sender_user_id" type="hidden" :value="user.id">
            <input name="recipient_user_id" type="hidden" :value="user_received.id">
            <input name="status" type="hidden" value="1">
            <div id="content-input-message">
                <input v-model="message" name="message" id="input_message" type="text" value="" placeholder="escreva sua mensagem">
                <button id="button-form" type="submit">enviar</button>
            </div>
        </form>
    </div>
</template> 
