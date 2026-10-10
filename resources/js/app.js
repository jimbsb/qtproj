//
import { validateForm } from "./modal/ticket.js";

function openModal(modal_event_name, data = null) {
    window.dispatchEvent(new CustomEvent(modal_event_name, {detail: data}));
}

window.modal = { validateTicketForm: validateForm, open: openModal };