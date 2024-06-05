<template>
    <Header />
    <div class="container">
        <div class="comic-cards">
            <a href="/characters" class="all-characters">All characters ></a>
            <section class="main-content">
                <div class="greeting">
                    <div class="greeting-content">
                        <h2 class="intro-title">Greetings Comicfan!</h2>
                        <p>Are you ready to dive deeper into the Marvel Comic Universe? 📚⭐</p>
                        <p>Create your ultimate wishlist and track your adventures through the multiverse. 🌌💥</p>
                    </div>
                    <a href="#" class="wishlist-button">Wishlist</a>
                </div>
                <div class="popular-characters">
                    <div class="characters-grid">
                        <h2 class="character-title">Featured Characters</h2>
                        <div class="character" v-for="character in popularCharacters" :key="character.id">
                            <img :src="character.thumbnail.path + '.' + character.thumbnail.extension"
                                :alt="character.name">
                            <router-link :to="{ name: 'character', params: { id: character.id } }"
                                class="character-name">{{ character.name }}</router-link>
                        </div>
                    </div>
                </div>
            </section>
            <section class="newest-releases">
                <div class="container new-rel-head">
                    <h2 class="title">Newest Releases</h2>
                    <a href="/comics" class="all-releases">All releases ></a>
                </div>
                <div class="container releases-carousel">
                    <div class="release" v-for="release in newestReleases" :key="release.id">
                        <div class="img-container">
                            <div class="circular-heart" @click="toggleWishlist(release)"
                                :class="{ 'wishlist-added': isInWishlist(release) }">
                                <i class="ri-heart-line icon"></i>
                            </div>
                            <img :src="release.thumbnail.path + '.' + release.thumbnail.extension" :alt="release.title">
                        </div>
                        <router-link :to="{ name: 'comic', params: { id: release.id } }" class="release-name">{{
                            release.title }}</router-link>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <Footer />
</template>

<script>
    import axios from 'axios';
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';

    const API_URL = 'https://gateway.marvel.com/v1/public';
    const API_KEY = 'e8d09a0b604fd41537ada8adabcf6b4b';
    const HASH = 'c7a4fd72acd5f90bf5b877329faea471';
    const TIMESTAMP = '1';

    export default {
        components: {
            Header,
            Footer
        },
        data() {
            return {
                comics: [],
                popularCharacters: [],
                newestReleases: []
            };
        },
        created() {
            this.loadCachedData();
            this.fetchDataWithBackoff('popularCharacters', this.fetchPopularCharacters);
            this.fetchDataWithBackoff('newestReleases', this.fetchNewestReleases);
        },
        methods: {
            loadCachedData() {
                const cachedCharacters = localStorage.getItem('popularCharacters');
                if (cachedCharacters) {
                    this.popularCharacters = JSON.parse(cachedCharacters);
                }
                const cachedReleases = localStorage.getItem('newestReleases');
                if (cachedReleases) {
                    this.newestReleases = JSON.parse(cachedReleases);
                }
            },
            async fetchDataWithBackoff(dataType, fetchFunction) {
                if (this[dataType].length > 0) {
                    return;
                }

                const maxRetries = 5;
                let retries = 0;
                const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

                while (retries < maxRetries) {
                    try {
                        await fetchFunction.call(this);
                        localStorage.setItem(dataType, JSON.stringify(this[dataType]));
                        break;
                    } catch (error) {
                        if (error.response && error.response.status === 429) {
                            retries++;
                            const waitTime = retries * 2000;
                            console.error(`Attempt ${retries} failed. Retrying in ${waitTime}ms...`);
                            await delay(waitTime);
                        } else {
                            console.error(`Failed to fetch ${dataType}:`, error);
                            break;
                        }
                    }
                }
            },
            async fetchPopularCharacters() {
                const response = await axios.get(`${API_URL}/characters?orderBy=-modified&limit=4&ts=${TIMESTAMP}&apikey=${API_KEY}&hash=${HASH}`);
                this.popularCharacters = response.data.data.results;
            },
            async fetchNewestReleases() {
                const response = await axios.get(`${API_URL}/comics?orderBy=-onsaleDate&limit=5&ts=${TIMESTAMP}&apikey=${API_KEY}&hash=${HASH}`);
                this.newestReleases = response.data.data.results;
                this.newestReleases.forEach(release => {
                    if (release.thumbnail.path.endsWith('image_not_available')) {
                        release.thumbnail.path = '/images/clean';
                    }
                });
            },
            isInWishlist(release) {
                let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                return wishlist.some(item => item.id === release.id);
            },

            toggleWishlist(release) {
                let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                const index = wishlist.findIndex(item => item.id === release.id);
                if (index === -1) {
                    wishlist.push(release);
                    localStorage.setItem('wishlist', JSON.stringify(wishlist));
                    alert(`${release.title} added to wishlist!`);
                } else {
                    wishlist.splice(index, 1);
                    localStorage.setItem('wishlist', JSON.stringify(wishlist));
                    alert(`${release.title} removed from wishlist!`);
                }
            },
        }
    };
</script>

<style scoped>
    .container {
        max-width: 1536px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .intro-title {
        font-size: 2em;
    }

    .title {
        font-size: 2em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .character-title {
        font-size: 1.5em;
        font-weight: bold;
    }

    .main-content {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .greeting {
        background-color: #FDC247;
        padding: 20px;
        border-radius: 20px;
        text-align: center;
        width: 36%;
        height: 277px;
    }

    .greeting-content {
        margin: 2% 0
    }

    .greeting p {
        max-width: 65%;
        margin: 3% auto;
    }

    .wishlist-button {
        background-color: #CA8A04;
        color: #fff;
        border: none;
        padding: 10px 30px;
        cursor: pointer;
        border-radius: 25px;
    }

    .popular-characters {
        width: 26%;
        height: 30rem;
        margin-left: 34%;
        background-color: #FDC247;
        border-radius: 20px;
    }

    .characters-grid {
        display: grid;
        text-align: center;
        padding: 20px;
    }

    .character {
        padding: 10px;
    }

    .character img {
        width: 155px;
        height: 66%;
        margin-bottom: 10px;
    }

    .character-name {
        font-size: 16px;
        font-weight: bold;
    }

    .characters-grid h2 {
        grid-column: span 2;
    }

    .all-releases {
        display: block;
        background-color: #d4a017;
        color: #fff;
        padding: 10px 8px;
        border-radius: 25px;
        text-decoration: none;
        text-align: center;
        margin: 10px auto 0.5%;
        width: fit-content;
    }

    .all-characters {
        display: block;
        background-color: #d4a017;
        color: #fff;
        padding: 10px 8px;
        border-radius: 25px;
        text-decoration: none;
        margin: 10px 0;
        width: fit-content;
        align-self: flex-end;
        margin-left: auto;
    }

    .newest-releases {
        margin-top: 70px;
    }

    .new-rel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .newest-releases h2 {
        flex: 1;
        text-align: center;
        margin: 0;
    }

    .releases-carousel {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        margin-bottom: 90px;
    }

    .release {
        position: relative;
        flex: 0 0 auto;
        margin: 0 15px;
        width: 200px;
        height: 300px;
    }

    .release img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .release-name {
        text-align: center;
        margin-top: 5px;
        font-size: 16px;
        font-weight: bold;
    }

    .circular-heart {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .circular-heart i {
        font-size: 1.5rem;
        color: #fff;
    }

    .wishlist-added {
        color: #d63031;
        background-color: #d63031;
    }
</style>