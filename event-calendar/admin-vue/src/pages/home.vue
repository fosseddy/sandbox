<script setup lang="ts">
import {ref} from "vue";

const msg = ref<string>("hello");
</script>

<template>
    <main>
        <h1>This is Home Page</h1>
        <p>{{msg}}</p>
    </main>
</template>
