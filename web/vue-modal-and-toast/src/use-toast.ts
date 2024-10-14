import * as Vue from "vue";

interface Message {
    id: number;
    value: string;
}

const TOAST_PROVIDER_KEY: Vue.InjectionKey<Vue.Ref<Message[]>> = Symbol();

export const ToastProvider: Vue.Plugin = {
    install(app) {
        app.provide(TOAST_PROVIDER_KEY, Vue.ref([]));
    }
};

let uuid = 0;

interface UseToastReturn {
    messages: Vue.Ref<Message[]>;
    add: (message: string) => void;
    remove: (id: number) => void;
}

export function useToast(): UseToastReturn {
    const messages = Vue.inject(TOAST_PROVIDER_KEY)!;

    function add(message: string): void {
        messages.value.push({id: uuid++, value: message});
    }

    function remove(id: number): void {
        messages.value = messages.value.filter(m => m.id !== id);
        if (messages.value.length === 0) {
            uuid = 0;
        }
    }

    return {messages, add, remove};
}
