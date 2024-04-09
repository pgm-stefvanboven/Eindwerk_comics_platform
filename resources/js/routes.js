import { createWebHistory, createRouter } from "vue-router";
import Header from "./vue-components/Header.vue";

const routes = [
    {
        name: "Header",
        path: "/",
        component: Header,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;