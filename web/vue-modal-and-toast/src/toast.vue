<script setup lang="ts">
import * as Vue from "vue";

import {useToast} from "./use-toast.js";

const toast = useToast();
</script>

<template>
    <Vue.Teleport to="#toast">
        <Vue.TransitionGroup tag="ul" class="toast">
            <li
                v-for="message in toast.messages.value"
                :key="message.id"
                @click="toast.remove(message.id)"
            >
                {{message.value}}
            </li>
        </Vue.TransitionGroup>
    </Vue.Teleport>
</template>

<style scoped>
.toast {
    margin: 0;
    padding: 0;
    list-style: none;
    z-index: 999;
    position: absolute;
    top: 0;
    right: .5rem;
}

.toast li {
    border: 1px solid black;
    padding: 1rem;
    background: white;
    margin-bottom: .5rem;
    width: 13rem;
}

.v-move,
.v-enter-active,
.v-leave-active {
    transition: all 0.3s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
    transform: translateX(2rem);
}

.v-leave-active {
    position: absolute;
    right: 0;
}
</style>
