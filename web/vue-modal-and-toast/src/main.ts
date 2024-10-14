import * as Vue from "vue";

import "./style.css";
import {ToastProvider} from "./use-toast.js";
import App from "./app.vue";

Vue
    .createApp(App)
    .use(ToastProvider)
    .mount("#app");
