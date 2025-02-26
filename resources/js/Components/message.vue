<script setup>
import { request } from '../utils/request'

</script>
<script>
export default { 
    props:['user','user_received','token'],
    data(){
        return {
            message:''
        }
    },

    methods:{
        emitForm($event){
            this.$emit('new_message',$event,this.message);

            this.message = ''

        }


    }


}
</script>

<template>
    <div>
        <form @submit.prevent="emitForm" action="/" method="post">
            <input type="hidden" name="_token" :value="token" autocomplete="off">
            <input v-model="message" name="message" type="text" value="" placeholder="escreva sua mensagem">
            <input name="sender_user_id" type="hidden" :value="user.id">
            <input name="recipient_user_id" type="hidden" :value="user_received.id">
            <input name="status" type="hidden" value="1">
            <button type="submit">enviar</button>
        </form>
    </div>
</template> 
