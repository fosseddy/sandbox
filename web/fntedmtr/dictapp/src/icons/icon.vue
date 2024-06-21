<script>
import Search from "./search.vue";
import Logo from "./logo.vue";
import Moon from "./moon.vue";
import Sun from "./sun.vue";
import PlayCircle from "./play-circle.vue";
import ChevronDown from "./chevron-down.vue";
import Loader from "./loader.vue";

export default {
    components: { Search, Logo, Moon, Sun, PlayCircle, ChevronDown, Loader },

    props: {
        name: {
            type: String,
            required: true,
            validator(val) {
                return [
                    "search",
                    "logo",
                    "moon",
                    "sun",
                    "play-circle",
                    "chevron-down",
                    "loader"
                ].includes(val);
            }
        },
        size: {
            type: String,
            default: "sm",
            validator(val) { return ["xsm", "sm", "md", "lg"].includes(val); }
        }
    }
};
</script>

<template>
    <component :is="name" class="icon" :class="size"></component>
</template>

<style scoped>
.icon {
    stroke-width: 1px;
    stroke: var(--color-secondary);
    fill: none;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.xsm {
    width: 1rem;
    height: 1rem;
}

.sm {
    width: 1.5rem;
    height: 1.5rem;
}

.md {
    width: 3rem;
    height: 3rem;
}

.lg {
    width: 4rem;
    height: 4rem;
}
</style>
