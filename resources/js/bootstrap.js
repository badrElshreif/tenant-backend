import axios from 'axios';
import Echo from "laravel-echo";
import Pusher from 'pusher-js';

// Make Pusher globally available
window.Pusher = Pusher;


window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


const currentSubdomain = window.location.hostname; // Get the current subdomain

window.Echo = new Echo({
    broadcaster: "reverb",
    host: `http://store1.localhost:8080` ,// `http://${currentSubdomain}:8080`,  // Use HTTP and subdomain dynamically
  //  wsHost: currentSubdomain,
    wsHost: `store1.localhost:8080`,
    wsPort: 8080,
    forceTLS: false,  // Disable TLS for HTTP
    disableStats: true,
    key: "euexp8tuq0ru99minesv",
});

console.log(window.Echo)

// window.Echo = new Echo({
//     broadcaster: 'reverb', // Use 'reverb' instead of 'pusher'
//     key: "euexp8tuq0ru99minesv", // Your Reverb app key
//     host: `http://websocket:8080`,
//     wsHost: window.location.hostname, // Reverb server host
//     wsPort: 8080, // Reverb server port
//     wssPort: 8080, // Reverb server port for secure connections
//     forceTLS: false, // Set to true if using HTTPS
//     disableStats: true,
//     enabledTransports: ['ws', 'wss'], // Enable WebSocket and secure WebSocket
// });
console.log("ddaaaaaaaaaaaaaa");

const subdomain = window.location.hostname.split('.')[0]; // Get subdomain dynamically

window.Echo.channel(`messages.store1`)
    .listen('message.sent', (event) => {
        console.log(`Message from store1:`, event.message);
    });
