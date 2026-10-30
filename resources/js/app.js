//
import Alpine from 'alpinejs';
import { validateForm } from "./modal/ticket.js";
window.Alpine = Alpine;
Alpine.start();

function openModal(modal_event_name, data = null) {
    window.dispatchEvent(new CustomEvent(modal_event_name, { detail: data }));
}

window.modal = { validateTicketForm: validateForm, open: openModal };

// window.debounce_event = (func, delay = 300) => {
//     console.log('debounce function called with delay:', delay);
//     let timeout;
//     return function (...args) {
//         clearTimeout(timeout);
//         console.log('Executing debounced function with args:', args);
//         timeout = setTimeout(() => {
//             func.apply(this, args);
//         }, delay);
//     };
// }

window.debounce_event = (func, delay = 300) => {
    console.log('debounce function called with delay:', delay, func);
    return Alpine.debounce(func, delay);
}