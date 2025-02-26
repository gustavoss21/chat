import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

// window.user_id = location.search.split('=')[2]
// window.new_message = {
//     message:'default',
//     is_new:false
// };

// Echo.private(`chat.${user_id}`)
//     .listen('SendMessage', (e) => {
//         console.log(e);
//         new_message = e;
//         new_message['message'] = e;
//         new_message['is_new'] = true;

//     });