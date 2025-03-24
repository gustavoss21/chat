import { request } from '../utils/request';

export class Message{

    static status_error = -1;
    static status_pending = 0;
    static status_success = 1;
    static status_viewed = 2;

    constructor(user_id,user_received_id){
        this.messages = [];
        this.user_id = user_id;
        this.user_received_id = user_received_id;

    }
    async getmessages(){
        let messages = await request('api/chat?sender_user_id='+this.user_id+'&recipient_user_id='+this.user_received_id)

        return messages
    }

    callUpdateMessage(messages){
        let messages_viewed = messages.filter(message=>(this.user_id === message.recipient_user_id && message.status !== this.status_viewed))
        if(!messages_viewed.length)return;

        this.updateMessageView(messages_viewed)
    }

    updateMessageView(messages){
        let method = {'_method':'PATCH'}
        let url = 'api/chat'

        messages.push(method);
        let body = JSON.stringify(messages);

            
        request(url,'PATCH',body,{'content-type':'application/json'})
            .then(response=>console.log(response))
            .catch(error=>console.error(error))
    }

    addMessage(form,msg){
        console.log('message method was invoked')
        let message = {
            id:0,
            sender_user_id:this.user_id,
            message:msg,
            status:this.status_pending,

        }

        this.messages.push(message);

        this.sendMessage(form)

        msg = '';
    }

    sendMessage(form){
        let headers = {
            'accept':'application/json',
            // 'content-type':'application/json'

        }
        let url_base = "http://127.0.0.1:8000/api/chat";
        let url = url_base+'?sender_user_id='+this.user_id+'&recipient_user_id='+this.user_received_id
        let formData = new FormData(form.target);

        request(url,'POST',formData,headers)
            .then((msg)=>{
                this.messages[this.messages.length -1] = msg;
            }
            ).catch((error)=>this.updateMessageStatusTemple('error'))
    }

    /**
     * updates the messages status
     * @param Number status
     * @param Number ?index
     */
     updateMessageStatusTemple(status=null, index=null){
        let message = {};
        
        if(!Message[status]) throw new TypeError('status error');

        if(!index){
            message = this.messages[this.messages.length - 1]
        }else{
            message = this.messages[index];
        }
        

        message.status = Message[status]

    }
}