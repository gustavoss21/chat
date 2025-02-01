<script>
import { computed, defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/vue3';


export default {
    props:['user'],
    data() {
        return{
            teste:'enos',
            users:['tese','smari','sfsoefiwe','dsfs'],
            url : "http://127.0.0.1:8000/chat"
        }
    },
    methods:{
        getContacts(){
            let headers = {
                'accept':'application/json'
            }

            let user_name = this.user['name'];

            this.request('http://127.0.0.1:8000/api/users/?name='+user_name)
        },

        async request(url,method='GET',body=null,heads={}){
            let myInit = {
                method: method,
                headers: heads,
            };

            let velue_return = null;

            if(method === 'POST'){
                if(!body) return console.error('o paramentro body, nao foi enviado!')
                myInit['body'] = body
            }
           
            let teste =  await fetch(url,myInit)
            .then(response => {
                if (!response.ok) {
                  console.log("Network response was not ok.");
                  
                }
                
               return response.json();
                        },
            ).then(response=>{
            
                this.users = response;
            })
            .catch(error => console.error(error));

            console.log(teste)


            return velue_return;
        },
    },

    mounted(){
        this.getContacts()
    }
}
</script>

<template>
    <Head title="lista de contatos" />
    <h1>chegamos</h1>
    <a  v-for="user_data in users" :href="url+'?send='+user.id+'&received='+user_data.id"> 
        <div class="contact">
            {{ user_data.name }}
        </div>
    </a>
</template>
