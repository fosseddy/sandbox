<script>
import Icon from "@/icons/icon.vue";

export default {
    components: { Icon },

    data() {
        return {
            theme: "light"
        };
    },

    computed: {
        themeIcon() {
            return this.theme == "light" ? "moon" : "sun";
        }
    },

    watch: {
        theme(cur, old) {
            document.body.classList.replace(`theme-${old}`, `theme-${cur}`);
        }
    },

    created() {
        document.body.classList.add(`theme-${this.theme}`);
    }
};
</script>

<template>
    <div class="theme-toggle">
        <input type="checkbox" v-model="theme" true-value="dark" false-value="light">
        <Icon :name="themeIcon" />
    </div>
</template>

<style scoped>
.theme-toggle {
    display: flex;
    align-items: center;
}

input {
    --track-width: 2rem;
    --track-height: 1rem;
    --track-padding: .1875rem;
    --thumb-width: .75rem;
    --thumb-color: white;

    appearance: none;
    width: var(--track-width);
    height: var(--track-height);
    border-radius: calc(var(--track-height) / 2);
    position: relative;
    display: flex;
    align-items: center;
    background-color: var(--color-secondary);
    margin: 0;
    margin-right: .5rem;
    transition: background-color 100ms linear;
}

input::after {
    content: "";
    border-radius: 50%;
    background-color: var(--thumb-color);
    width: var(--thumb-width);
    height: var(--thumb-width);
    position: absolute;
    left: var(--track-padding);
    transition: transform 100ms linear;
}

input:checked {
    background-color: var(--color-primary);
}

input:checked::after {
    transform: translateX(
        calc(var(--track-width) - var(--track-padding) -
             var(--thumb-width) - var(--track-padding))
    );
}
</style>

<style>
body,
main,
input,
select {
    transition: background-color 100ms linear;
}

hr {
    transition: border-color 100ms linear;
}

.theme-light {
    --color-bg: white;
    --color-fg: black;
    --color-primary: purple;
    --color-secondary: gray;
    --color-accent: #eee;
}

.theme-dark {
    --color-bg: black;
    --color-fg: white;
    --color-primary: purple;
    --color-secondary: gray;
    --color-accent: #2b2929;
}
</style>
