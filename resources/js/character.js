import { createApp } from "vue";
import CharacterPage from "./CharacterDetail.vue";
import router from "./routes.js";


createApp(CharacterPage).use(router).mount("#character");