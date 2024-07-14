import { createApp } from "vue";
import CollectionPage from "./Collection.vue";
import router from "./routes.js";


createApp(CollectionPage).use(router).mount("#collection");