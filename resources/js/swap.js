import { createApp } from "vue";
import SwapRequestPage from "./SwapRequest.vue";
import router from "./routes.js";


createApp(SwapRequestPage).use(router).mount("#swap-request");