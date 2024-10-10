import * as Vue from "vue";

const TOAST_PROVIDER_KEY = Symbol();

export const ToastProvider = {
    install(app, opts) {
        app.provide(TOAST_PROVIDER_KEY, Vue.ref([]));
    }
};

let uuid = 0;

export function useToast() {
    const messages = Vue.inject(TOAST_PROVIDER_KEY);

    function add(message) {
        messages.value.push({id: uuid++, value: message});
        console.log(messages.value.map(m => m.id));
    }

    function remove(id) {
        messages.value = messages.value.filter(m => m.id !== id);
        if (messages.value.length === 0) {
            uuid = 0;
            console.log("uuid reset");
        }
        console.log(messages.value.map(m => m.id));
    }

    return {messages, add, remove};
}
