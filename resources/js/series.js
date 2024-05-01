import { createApp } from "vue";
import SeriesPage from "./Series.vue";
import router from "./routes.js";


createApp(SeriesPage).use(router).mount("#series");