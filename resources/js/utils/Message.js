import { request } from '../utils/request';

export class Message{

    static status_error = -1;
    static status_pending = 0;
    static status_success = 1;
    static status_viewed = 2;

    static status_list = [
        this.status_error,
        this.status_pending,
        this.status_success,
        this.status_viewed,
        this.status_list
    ]

    constructor(user_id,user_received_id){
        this.messages = [];
        this.user_id = user_id;
        this.user_received_id = user_received_id;
        this.messageDay = 0

    }
    async getmessages(){
        let messages = request('api/chat?sender_user_id='+this.user_id+'&recipient_user_id='+this.user_received_id)
        return messages;
    }

    setMessage(messages){
        messages.forEach((element,index)=>{
            let date = element.created_at
            element.created_at = this.dateFormat(date);
            element['time'] = this.getTimeMessage(date)
            this.messages.push(element)
        })
    }

    getTimeMessage(message_date){
        let options = {minute: "numeric",hour: "numeric"}

        let date = new Date(message_date)
        return date.toLocaleTimeString('pt-BR',options)
    }

    dateFormat(message_date){
        let options = {
            year: "numeric",
            month: "long",
            day: "numeric",
            };

        let date = new Date(message_date)
        let nowDate =new Date(Date.now());
        
        if(
                date.getFullYear() === nowDate.getFullYear() && date.getMonth() === nowDate.getMonth()
        ){
            
            if(date.getDate() === this.messageDay)return false;

            if((nowDate.getDate() - date.getDate()) < 7 ){
                options = {weekday: "long"};
            }
        }



        this.messageDay = date.getDate()

        return date.toLocaleDateString("pt-BR",options)
    }

    /**
     * updates the message view status
     */
    callUpdateMessage(messages){
        let messages_viewed = messages.filter(message=>(this.user_id === message.recipient_user_id && message.status !== Message.status_viewed))
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
            ).catch((error)=>this.updateMessageStatusTemple(Message.status_error))
    }

    /**
     * updates the messages status
     * @param Number status
     * @param Number ?index
     */
     updateMessageStatusTemple(status=null, index=null){
        let message = {};
        let index_current = index || this.messages.length - 1;

        if(Message.status_list.indexOf(status) === -1) throw new TypeError('status error');

        message = this.messages[index_current]
        
        message.status = status;

    }
}