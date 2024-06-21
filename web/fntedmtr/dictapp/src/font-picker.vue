<script>
import Icon from "@/icons/icon.vue";

export default {
    components: { Icon },

    data() {
        return {
            font: "Serif"
        };
    },

    watch: {
        font(cur, old) {
            document.body.classList.replace(`font-${old}`, `font-${cur}`);
        }
    },

    created() {
        document.body.classList.add(`font-${this.font}`);
    }
};
</script>

<template>
    <div class="font-picker">
        <select v-model="font">
            <option>Serif</option>
            <option>Mono</option>
            <option>Sans</option>
        </select>
        <Icon name="chevron-down" size="xsm" />
    </div>
</template>

<style scoped>
.font-picker {
    display: flex;
    align-items: center;
    position: relative;
}

.font-picker select {
    appearance: none;
    border: none;
    background-color: var(--color-bg);
    padding-right: 1.25rem;
}

.font-picker .icon {
    pointer-events: none;
    stroke: var(--color-primary);
    position: absolute;
    right: 0;
    stroke-width: 2px;
}
</style>

<style>
.font-Serif {
    font-family: serif;
}

.font-Mono {
    font-family: monospace;
}

.font-Sans {
    font-family: sans-serif;
}
</style>
