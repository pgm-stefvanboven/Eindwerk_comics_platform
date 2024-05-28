import { createApp } from "vue";
import WishlistPage from "./Wishlist.vue";
import router from "./routes.js";


createApp(WishlistPage).use(router).mount("#wishlist");