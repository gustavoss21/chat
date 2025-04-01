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
    ]

    static status_class = {
        [this.status_error]:'icon-error',
        [this.status_pending]:'icon-pending',
        [this.status_success]:'icon-success',
        [this.status_viewed]:'icon-viewed',
    }

    constructor(user_id){
        this.messages = [];
        this.user_id = user_id;
        this.messageDay = 0

    }
    async getmessages(user_received_id){
        let messages = request('api/chat?sender_user_id='+this.user_id+'&recipient_user_id='+user_received_id)
        return messages;
    }

    setMessage(messages){
        messages.forEach((message)=>{
            let message_with_date = this.setMessageDate(message)
            this.messages.push(message_with_date)
        })
    }

    updateMessage(message,index){
        message = this.setMessageDate(message)
        this.messages.splice(index,1,message);
    }
    /**
     * nao mexe no this
     * @param {Object} message_data 
     * @return message_data
     */
    setMessageDate(message_data){
        let date = message_data.created_at
        message_data.status = message_data.status || Message.status_pending
        message_data.created_at = this.dateFormat(date);
        message_data['time'] = this.getTimeMessage(date)
        message_data['status_class'] = Message.status_class[message_data.status]
        return message_data
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

        if(date.getDate() === this.messageDay)return false;
        
        if(
            date.getFullYear() === nowDate.getFullYear()){

            let diff_month = nowDate.getMonth() - date.getMonth();
            let ultimoDia = (new Date(date.getFullYear(), date.getMonth()+1, 0)).getDate();
            let diff_day = (ultimoDia - date.getDate()) + nowDate.getDate()
            
            if(!(diff_month <= 1 && diff_day < 7))return;
            
            options = {weekday: "long"};
            
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
            created_at: new Date(Date.now())

        }
        
        this.setMessage([message])

        // this.messages.push(message);

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
                let index = this.messages.length -1
                this.updateMessage(msg,index)
                this.updateMessageStatusTemple(Message.status_success)
            }
            ).catch((error)=>this.updateMessageStatusTemple(Message.status_error,null,true))
    }

    /**
     * updates the messages status
     * @param Number status
     * @param Number ?index
     */
     updateMessageStatusTemple(status=null, index=null){
        let index_current = index || this.messages.length - 1;

        if(Message.status_list.indexOf(status) === -1) throw new TypeError('status error');
        
        this.messages[index_current].status = status; 
        this.messages[index_current]['status_class'] = Message.status_class[status]
     }
}