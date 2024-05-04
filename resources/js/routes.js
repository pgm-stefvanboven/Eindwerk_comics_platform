import { createWebHistory, createRouter } from "vue-router";
import Header from "./vue-components/Header.vue";
import Footer from "./vue-components/Footer.vue";
import SeriesPage from "./Series.vue";

const routes = [
    {
        name: "Header",
        path: "/",
        component: Header,
    },
    {
        name: "SeriesPage",
        path: "/series",
        component: SeriesPage,
    },
    {
        name: "Footer",
        path: "/",
        component: Footer,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
