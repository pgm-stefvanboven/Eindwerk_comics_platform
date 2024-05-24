import { createApp } from "vue";
import ComicPage from "./ComicDetail.vue";
import router from "./routes.js";


createApp(ComicPage).use(router).mount("#comic");