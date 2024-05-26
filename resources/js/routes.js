import { createWebHistory, createRouter } from "vue-router";
import Home from "./Home.vue";
import ComicsPage from "./Comics.vue";
import ComicDetail from "./ComicDetail.vue";
import CharactersPage from "./Characters.vue";
import CharacterDetail from "./CharacterDetail.vue";

const routes = [
    {
        name: "Home",
        path: "/",
        component: Home,
    },
    {
        name: "ComicsPage",
        path: "/comics",
        component: ComicsPage,
    },
    {
        path: "/comic/:id",
        name: "comic",
        component: ComicDetail,
        props: true,
    },
    {
        name: "CharactersPage",
        path: "/characters",
        component: CharactersPage,
    },
    {
        path: "/character/:id",
        name: "character",
        component: CharacterDetail,
        props: true,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
