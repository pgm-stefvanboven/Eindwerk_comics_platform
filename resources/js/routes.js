import { createWebHistory, createRouter } from "vue-router";
import Home from "./Home.vue";
import ComicsPage from "./Comics.vue";
import ComicDetail from "./ComicDetail.vue";
import SeriesPage from "./Series.vue";

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
        name: "SeriesPage",
        path: "/series",
        component: SeriesPage,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;