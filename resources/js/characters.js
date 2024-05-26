import { createApp } from "vue";
import CharactersPage from "./Characters.vue";
import router from "./routes.js";


createApp(CharactersPage).use(router).mount("#characters");