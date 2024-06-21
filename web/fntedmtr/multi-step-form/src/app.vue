<script>
export default {
    data() {
        return {
            tabamout: 4,
            tab: 1,
            aside: ["YOUR INFO", "SELECT PLAN", "ADD-ONS", "SUMMARY"]
        };
    },

    methods: {
        goto(index) {
            if (index > this.tabamout) {
                index = this.tabamout;
            } else if (index < 1) {
                index = 1;
            }

            if (index == this.tab) return;
            this.tab = index;
        },

        next() {
            this.goto(this.tab + 1);
        },

        prev() {
            this.goto(this.tab - 1);
        },

        submit() {
            console.log("submitting data...");
        }
    }
};
</script>

<template>
    <div class="container">
        <div class="nav">
            <ul>
                <li v-for="(it, index) in aside" :key="index" class="box">
                    <div :class="{'active': index + 1 == tab}">{{index + 1}}</div>
                    <div>
                        <p>STEP {{index + 1}}</p>
                        <p>{{it}}</p>
                    </div>
                </li>
            </ul>
        </div>

        <div class="tab">
            <section v-if="tab == 1">
                <h2>Personal info</h2>
                <p>Please provide your name, email, address, and phone number.</p>
            </section>

            <section v-else-if="tab == 2">
                <h2>Select your plan</h2>
                <p>You have the option of monthly or yearly billing.</p>
            </section>

            <section v-else-if="tab == 3">
                <h2>Pick add-ons</h2>
                <p>Add-ons help enhance your gaming experience.</p>
            </section>

            <section v-else>
                <h2>Finishing up</h2>
                <p>Double-check everything looks OK before confirming.</p>
            </section>
        </div>

        <div class="footer">
            <div>
                <button v-if="tab > 1" class="btn-goback" @click="prev">Go Back</button>
            </div>
            <button v-if="tab < tabamout" @click="next">Next Step</button>
            <button v-else class="btn-action" @click="submit">Confirm</button>
        </div>
    </div>
</template>

<style scoped>
.container {
    display: grid;
    grid-template-columns: minmax(15rem, 1fr) minmax(20rem, 2fr);
    grid-template-rows: 1fr 4rem;
    background: white;
    border-radius: 5px;
    padding: 1rem;
    column-gap: 2rem;
    min-height: 30rem;
}

.nav {
    grid-row: 1/3;
    padding: .5rem 1.5rem;
    background: cornflowerblue;
    color: white;
    border-radius: 5px;
}

.nav ul li:not(:last-child) {
    margin-bottom: 1rem;
}

.box {
    display: flex;
    align-items: center;
}

.box > :first-child {
    margin-right: 1rem;
    border: 1px solid white;
    border-radius: 50%;
    padding: .3rem .6rem;
}

.box > :first-child.active {
    background: white;
    color: black;
}

.box > :nth-child(2) p {
    margin: 0;
    font-weight: bold;
}

.box > :nth-child(2) p:first-child {
    color: lightgray;
    font-weight: normal;
    font-size: .9rem;
}

button {
    cursor: pointer;
    padding: 0.5rem 1rem;
    margin: 0;
    border: none;
    background: navy;
    color: white;
    border-radius: 5px
}

button:hover {
    opacity: .8;
}

.btn-goback {
    color: gray;
    background: transparent;
    padding-left: 0;
    padding-right: 0;
}

.btn-action {
    background: cornflowerblue;
}

.footer {
    padding: 0 1rem;
    background: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

section {
    padding: 1rem;
}

section p {
    color: gray;
}

@media (max-width: 44rem) {
    .container {
        background: lightgray;
        height: 100%;
        padding: 0;

        grid-template-columns: 1fr;
        grid-template-rows: 10rem minmax(25rem, auto) 4rem;
    }

    .nav {
        border-radius: 0;
        grid-row: 1;
    }

    .nav ul {
        display: flex;
        justify-content: center;
    }

    .nav ul li:not(:last-child) {
        margin-bottom: 0;
    }

    .box > :nth-child(2) p {
        display: none;
        margin: 0;
        font-weight: bold;
    }

    .box > :not(:last-child) {
        margin-right: 1rem;
    }

    .tab {
        background: white;
        border-radius: 5px;
        margin: 0 1rem;
        position: relative;
        top: -4rem;
    }
}
</style>

<!-- Global Styles -->
<style>
*,
*::after,
*::before {
    box-sizing: border-box;
}

ul {
    list-style: none;
    padding: 0;
}

html,
body,
#app {
    height: 100%;
}

body {
    display: grid;
    place-content: center;
    background: lightgray;
}

@media (max-width: 44rem) {
    body {
        display: block;
        margin: 0;
    }
}
</style>
