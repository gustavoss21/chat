<script setup>
import { request } from '../utils/request'
import DropdownLink from '@/Components/DropdownLink.vue';

</script>
<script>
export default { 
    props:['user','token'],
    data(){
        return {
            
        }
    },

    methods:{

    }


}
</script>

<template>
    <nav>
        <ul class="navbar">
            <li><a href="contact-us">Fale conosco</a></li>
            <template v-if="user">
                <li>{{ user.name }}</li>
                <li><DropdownLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                >
                    sair
                </DropdownLink>
            </li>
                
            </template>
            <template v-else>
                <li><a href="login">login</a></li>
            </template>
        </ul>
    </nav>
</template> 
