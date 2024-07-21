import { createApp } from "vue";
import ListingListPage from "./ListingList.vue";
import router from "./routes.js";


createApp(ListingListPage).use(router).mount("#listing-list");