<script setup>
import * as Vue from "vue";

defineProps({open: Boolean});
defineEmits(["close"]);

</script>

<template>
    <Vue.Teleport to="#modal">
        <Vue.Transition>
            <div v-if="open" class="modal">
                <div class="container">
                    <h2>hello, world</h2>
                    <slot></slot>
                    <button @click="$emit('close')">close modal</button>
                </div>
            </div>
        </Vue.Transition>
    </Vue.Teleport>
</template>

<style scoped>
.modal {
    z-index: 999;
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background: rgba(0, 0, 0, .5);
}

.v-enter-active,
.v-leave-active {
    transition: opacity 0.3s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}

.container {
    background: white;
    padding: 1rem;
    border-radius: 5px;
    box-shadow: 0px 5px 10px 2px #000;
}

.container button {
    display: block;
}
</style>
