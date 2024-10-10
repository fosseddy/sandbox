<script setup>
import * as Vue from "vue";

import Modal from "./modal.vue";
import Toast from "./toast.vue";
import {useToast} from "./use-toast.js";

const open = Vue.ref(false);
const toast = useToast();
</script>

<template>
    <button @click="open = true">open modal</button>
    <button @click="toast.add(Math.random())">
        push message
    </button>

    <Modal :open="open" @close="open = false">
        <p>This is modal content</p>
    </Modal>

    <Toast />
</template>

<style scoped>
button {
    display: block;
}
</style>
