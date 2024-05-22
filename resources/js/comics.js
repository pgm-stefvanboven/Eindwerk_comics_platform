import { createApp } from "vue";
import ComicsPage from "./Comics.vue";
import router from "./routes.js";


createApp(ComicsPage).use(router).mount("#comics");