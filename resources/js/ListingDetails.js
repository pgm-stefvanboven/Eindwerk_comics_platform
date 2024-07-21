import { createApp } from "vue";
import ListingDetailPage from "./ListingDetails.vue";
import router from "./routes.js";


createApp(ListingDetailPage).use(router).mount("#listing-details");