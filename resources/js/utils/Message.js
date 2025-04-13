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
        this.messages_to_add = []
        this.send_message_status = false
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

    updateMessage(message,index_arg){
        if(!this.messages[index_arg])return;

        message = this.setMessageDate(message)
        this.messages.splice(index_arg,1,message);
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

        if(date.getDate() === this.messageDay)return
        
        if(
            date.getFullYear() === nowDate.getFullYear()){

            let diff_month = nowDate.getMonth() - date.getMonth();
            let ultimoDia = (new Date(date.getFullYear(), date.getMonth()+1, 0)).getDate();
            let diff_day = (ultimoDia - date.getDate()) + nowDate.getDate()
            
            if(!(diff_month <= 1 && diff_day < 7))return
            
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
        if(!msg)return

        let message = {
            sender_user_id:this.user_id,
            message:msg,
            status:this.status_pending,
            created_at: new Date(Date.now())

        }
        
        this.setMessage([message])

        // this.messages.push(message);
        if(this.send_message_status){
            let form_data = form.target.cloneNode(true)
            this.messages_to_add.push(form_data)
            return
        }

        this.sendMessage(form,this.messages.length - 1)

        msg = '';
        this.send_message_status = true

    }

    resendMessage(index){
        this.send_message_status = false;

        if(!(this.messages_to_add.length >= 1))return

        this.sendMessage(this.messages_to_add.shift(),index)
    }

    sendMessage(form,index){
        let headers = {
            'accept':'application/json',
            // 'content-type':'application/json'

        }
        let url_base = "http://127.0.0.1:8000/api/chat";
        let url = url_base+'?sender_user_id='+this.user_id+'&recipient_user_id='+this.user_received_id
        let formData = new FormData(form.target||form
            
        );
        

        return request(url,'POST',formData,headers)
            .then((msg)=>{
                this.updateMessage(msg,index)
                this.updateMessageStatusTemple(Message.status_success,index)
                this.resendMessage(index+1)

            }
            ).catch((error)=>{
                this.updateMessageStatusTemple(Message.status_error,index);
                console.log(error);
                
                if(this.messages_to_add.length >= 1){
                    this.resendMessage()
                }})
    }

    /**
     * updates the messages status
     * @param Number status
     * @param Number ?index
     */
     updateMessageStatusTemple(status, index){
        if(!this.messages[index])return;

        if(Message.status_list.indexOf(status) === -1) throw new TypeError('status error');
        
        this.messages[index].status = status; 
        this.messages[index]['status_class'] = Message.status_class[status]
     }
}