import { createApp } from "vue";
import CollectionDetailPage from "./CollectionDetail.vue";
import router from "./routes.js";


createApp(CollectionDetailPage).use(router).mount("#collectionDetail");