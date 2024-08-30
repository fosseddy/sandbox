import {createRouter, createWebHistory} from "vue-router";
import Home from "./pages/home.vue";

export const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home",
            component: Home
        },
        {
            path: "/about",
            name: "about",
            component: () => import("./pages/about.vue")
        }
    ]
});
