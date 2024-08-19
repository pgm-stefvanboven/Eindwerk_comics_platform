import { createWebHistory, createRouter } from "vue-router";
import Home from "./Home.vue";
import ComicsPage from "./Comics.vue";
import ComicDetail from "./ComicDetail.vue";
import CharactersPage from "./Characters.vue";
import CharacterDetail from "./CharacterDetail.vue";
import WishlistPage from "./Wishlist.vue";
import CommunityPage from "./Community.vue";
import CollectionPage from "./Collection.vue";
import CollectionDetailPage from "./CollectionDetail.vue";
import StorePage from "./Store.vue";
import SwapRequestPage from "./SwapRequest.vue";

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
    {
        name: "WishlistPage",
        path: "/wishlist",
        component: WishlistPage,
    },
    {
        name: "CommunityPage",
        path: "/community",
        component: CommunityPage,
    },
    {
        name: "collection",
        path: "/collection",
        component: CollectionPage,
    },
    {
        path: "/collection/:id",
        name: "collectionDetail",
        component: CollectionDetailPage,
        props: true,
    },
    {
        name: "StorePage",
        path: "/store",
        component: StorePage,
    },
    {
        name: "SwapRequestPage",
        path: "/swap-request",
        component: SwapRequestPage,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
