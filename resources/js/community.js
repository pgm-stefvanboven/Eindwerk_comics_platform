import { createApp } from "vue";
import CommunityPage from "./Community.vue";
import router from "./routes.js";


createApp(CommunityPage).use(router).mount("#community");