<template>
    <div>
        <Header />
        <h1 class="title">Wishlist</h1>
        <div v-if="wishlist.length === 0" class="empty-content">
            <p class="empty-txt">
                Your wishlist is currently empty, but the adventure is just beginning! Start adding your favorite comics
                and build your ultimate collection.
            </p>
            <p>
                <router-link to="/comics" class="browse-link">Browse Comics Now</router-link>
            </p>
        </div>
        <div v-else class="comics-carousel">
            <div v-for="comic in wishlist" :key="comic.id" class="comic">
                <div class="img-container">
                    <div class="circular-heart" @click="toggleWishlist(comic)"
                        :class="{ 'wishlist-added': isInWishlist(comic) }">
                        <i class="ri-heart-line icon"></i>
                    </div>
                    <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title">
                </div>
                <router-link :to="{ name: 'comic', params: { id: comic.id } }" class="comic-name">{{ comic.title }}</router-link>
            </div>
        </div>
    </div>
    <Footer />
</template>


<script>
    import axios from 'axios';
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';

    export default {
        components: {
            Header,
            Footer
        },
        data() {
            return {
                wishlist: [],
            };
        },
        created() {
            this.loadWishlist();
        },
        methods: {
            loadWishlist() {
                const storedWishlist = localStorage.getItem('wishlist');
                if (storedWishlist) {
                    this.wishlist = JSON.parse(storedWishlist);
                }
            },
            isInWishlist(comic) {
                return this.wishlist.some(item => item.id === comic.id);
            },
            toggleWishlist(comic) {
                const index = this.wishlist.findIndex(item => item.id === comic.id);
                if (index === -1) {
                    this.wishlist.push(comic);
                    localStorage.setItem('wishlist', JSON.stringify(this.wishlist));
                    alert(`${comic.title} added to wishlist!`);
                } else {
                    this.wishlist.splice(index, 1);
                    localStorage.setItem('wishlist', JSON.stringify(this.wishlist));
                    alert(`${comic.title} removed from wishlist!`);
                }
            },
        }
    }
</script>

<style scoped>
    .title {
        text-align: center;
        margin-top: 1rem;
        font-size: 2em;
        font-weight: bold;
    }

    .empty-content {
        max-width: 740px;
        text-align: center;
        margin: 2rem auto;
    }

    .empty-txt {
        text-align: center;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .browse-link {
        display: inline-block;
        margin-top: 1rem;
        padding: 0.5rem 1rem;
        background-color: #007bff;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .browse-link:hover {
        background-color: #0056b3;
    }

    .comics-carousel {
        display: flex;
        justify-content: unset;
        margin-top: 10px;
    }

    .comic {
        flex: 0 0 auto;
        margin-left: 29.5px;
        margin-right: 75.3px;
        width: 200px;
        height: 300px;
    }

    .comic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .img-container {
        position: relative;
        width: 100%;
        padding-top: 150%;
        overflow: hidden;
    }

    .img-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .circular-heart {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background-color: white;
        border-radius: 50%;
        width: 2rem;
        height: 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .circular-heart.wishlist-added {
        background-color: red;
    }

    .icon {
        font-size: 1.5rem;
    }

    .comic-name {
        text-align: center;
        margin-top: 5px;
        font-size: 16px;
        font-weight: bold;
    }

    .comic-name:hover {
        text-decoration: underline;
    }

    .footer {
        margin-top: 304px;
    }

    .popup {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        z-index: 1000;
    }

    .popup button {
        background-color: transparent;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 16px;
    }

    button {
        background-color: #CA8A04;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        transition-duration: 0.4s;
        border-radius: 25px;
    }

    button:hover {
        background-color: #FFD700;
    }

    .button:hover {
        background-color: #FFD700;
    }
</style>