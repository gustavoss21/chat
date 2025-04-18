import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import Message from '@/Components/message.vue';
import listContact from '@/Pages/listContact.vue';
import chatMessage from '@/Pages/chatMessage.vue';
import contactUs from '@/Pages/contactUs.vue';
import contactUsMain from '@/Pages/contactUsMain.vue';
import NavBar from '@/Components/NavBar.vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('Message',Message)
            .component('listContact',listContact)
            .component('chatMessage',chatMessage)
            .component('contactUs',contactUs)
            .component('contactUsMain',contactUsMain)
            .component('NavBar',NavBar)
            .mount(el)

    },
    progress: {
        color: '#4B5563',
    },
});
