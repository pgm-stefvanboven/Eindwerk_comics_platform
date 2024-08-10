import { createApp } from "vue";
import StorePage from "./Store.vue";
import router from "./routes.js";


createApp(StorePage).use(router).mount("#store");