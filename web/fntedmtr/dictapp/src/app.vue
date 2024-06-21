<script>
import Icon from "@/icons/icon.vue"
import FontPicker from "@/font-picker.vue";
import ThemeToggle from "@/theme-toggle.vue";

export default {
    components: { Icon, FontPicker, ThemeToggle },

    data() {
        return {
            word: "",
            loading: false,
            data: null,
            error: null,
            audio: new Audio()
        };
    },

    methods: {
        async queryDictionary() {
            this.word = this.word.trim();
            if (!this.word.length) return;

            this.loading = true;
            this.error = null
            this.data = null;

            const apiURL = `https://api.dictionaryapi.dev/api/v2/entries/en/${this.word}`;
            try {
                const res = await fetch(apiURL);
                if (!res.ok) {
                    this.error = await res.json();
                    this.loading = false;
                    return;
                }

                this.data = await res.json();
                this.data = this.data[0];

                // @Note(art): if audio is present then canplaythough or error
                // event listeners stop the page loading state
                const p = this.data.phonetics.find(it => it.audio?.length > 0);
                if (p) {
                    this.data.hasAudio = true;
                    this.audio.src = p.audio;
                    this.audio.load();
                } else {
                    this.loading = false;
                }
            } catch(err) {
                console.error(err);
                this.error = {
                    title: "Something Went Wrong",
                    message: "Sorry pal, something went wrong, try checking your internet connection."
                }
                this.loading = false;
            }
        },

        stopLoading() {
            if (this.loading) {
                this.loading = false;
            }
        },

        handleAudioLoadError(e) {
            console.error(this.audio.error);
            this.data.hasAudio = false;
            if (this.loading) {
                this.loading = false;
            }
        }
    },

    created() {
        this.audio.addEventListener("canplaythrough", this.stopLoading);
        this.audio.addEventListener("error", this.handleAudioLoadError);
    },

    unmounted() {
        this.audio.removeEventListener("canplaythrough", this.stopLoading);
        this.audio.removeEventListener("error", this.handleAudioLoadError);
    }
};
</script>

<template>
    <header class="app-header">
        <div class="top-line">
            <Icon name="logo" size="md" />
            <div class="settings">
                <FontPicker />
                <hr>
                <ThemeToggle />
            </div>
        </div>

        <form @submit.prevent="queryDictionary">
            <input v-model="word">
            <Icon name="search" size="xsm" />
        </form>
    </header>

    <main>
        <div v-if="loading" class="loader">
            <Icon name="loader" size="md" />
        </div>
        <div v-else-if="error">
            <div class="section-header">
                <h2>{{ error.title }}</h2>
                <hr>
            </div>
            <p class="section-subheader">{{ error.message }}</p>
        </div>
        <div v-else-if="data" class="word-info">
            <header>
                <div>
                    <h1>{{ data.word }}</h1>
                    <p>{{ data.phonetic }}</p>
                </div>
                <button v-if="data.hasAudio" @click="audio.play">
                    <Icon name="play-circle" size="lg" />
                </button>
            </header>
            <section v-for="m in data.meanings" :key="m.partOfSpeech">
                <div class="section-header">
                    <h2>{{ m.partOfSpeech }}</h2>
                    <hr>
                </div>
                <p class="section-subheader">Meaning</p>
                <ul>
                    <template v-for="d in m.definitions" :key="d.definition">
                        <li>{{ d.definition }}</li>
                        <li v-if="d.example" class="def-example">"{{ d.example }}"</li>
                    </template>
                </ul>
                <p v-if="m.synonyms.length">
                    <span class="section-subheader">Synonyms</span>
                    <template v-for="it, idx in m.synonyms" :key="idx">
                        <span class="kwd">{{ it }}</span>{{ idx == m.synonyms.length-1 ? "" : ", " }}
                    </template>
                </p>
                <p v-if="m.antonyms.length">
                    <span class="section-subheader">Antonyms</span>
                    <template v-for="it, idx in m.antonyms" :key="idx">
                        <span class="kwd">{{ it }}</span>{{ idx == m.antonyms.length-1 ? "" : ", " }}
                    </template>
                </p>
            </section>
            <template v-if="data.sourceUrls.length">
                <hr>
                <p class="source">
                    Source <a :href="data.sourceUrls[0]" target="_blank">{{ data.sourceUrls[0] }}</a>
                </p>
            </template>
        </div>
    </main>
</template>

<style scoped>
@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

main {
    color: var(--color-fg);
    background-color: var(--color-bg);
}

.loader {
    display: flex;
    align-items: center;
    justify-content: center;
}

.loader .icon {
    animation: 3s linear rotate infinite;
}

.app-header {
    margin: 2rem 0;
}

.app-header .top-line {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.app-header .settings {
    display: flex;
    align-items: center;
}

.app-header .settings hr {
    margin: 0 1rem;
    align-self: stretch;
}

.app-header form {
    display: flex;
    align-items: center;
    position: relative;
    width: 100%;
}

.app-header form input {
    width: 100%;
    padding: 1rem;
    padding-right: 3rem;
    border-radius: 10px;
    border: none;
    background-color: var(--color-accent);
    font-weight: bold;
}

.app-header form .icon {
    position: absolute;
    right: 1rem;
    stroke: var(--color-primary);
    stroke-width: 2px;
    pointer-events: none;
}

.word-info header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.word-info header h1 {
    font-size: 3rem;
    margin: 0;
}

.word-info header p {
    margin: 0;
    margin-bottom: 1rem;
    font-size: 1.25rem;
    color: var(--color-primary);
}

.word-info header button {
    background: transparent;
    border: none;
    padding: 0;
}

.word-info header button:hover {
    cursor: pointer;
    opacity: .6;
}

.word-info header button:active {
    transform: scale(.95);
}

.word-info header button .icon {
    stroke: var(--color-primary);
}

.section-header {
    display: flex;
    align-items: center;
}

.section-header h2 {
    font-size: 1.125rem;
    margin-right: 1.25rem;
}

.section-header hr {
    width: 100%;
    flex: 1;
}

.section-subheader {
    color: var(--color-secondary);
    font-size: 1.125rem;
}

.word-info section span.section-subheader {
    margin-right: 1rem;
}

.word-info .kwd {
    color: var(--color-primary);
    font-weight: bold;
}

.word-info ul li:not(:last-child) {
    margin-bottom: .75rem;
}

.word-info ul li::marker {
    color: var(--color-primary);
}

.word-info ul li.def-example {
    list-style: none;
    color: var(--color-secondary);
}

.word-info .source {
    color: var(--color-secondary);
    font-size: .875rem;
}

.word-info .source a {
    color: var(--color-fg);
}

.word-info .source a:hover {
    opacity: .6
}
</style>
