<template>
    <div>
        <Header />
        <div class="comic-cards">
            <a href="" class="all-characters">All characters ></a>
            <section class="main-content">
                <div class="greeting">
                    <div class="greeting-content">
                        <h2>Greetings Comicfan!</h2>
                        <p>Are you ready to dive deeper into the Marvel Comic Universe? 📚⭐</p>
                        <p>Create your ultimate wishlist and track your adventures through the multiverse. 🌌💥</p>
                    </div>
                    <a href="#" class="wishlist-button">Wishlist</a>
                </div>

                <div class="popular-characters">
                    <div class="characters-grid">
                        <h2>Featured Characters</h2>
                        <div class="character" v-for="character in popularCharacters" :key="character.id">
                            <img :src="character.thumbnail.path + '.' + character.thumbnail.extension"
                                :alt="character.name">
                            <p class="character-name">
                                {{ character.name }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="newest-releases">
                <h2>Newest Releases</h2>
                <a href="/comics" class="all-releases">All releases ></a>
                <div class="releases-carousel">
                    <div class="release" v-for="release in newestReleases" :key="release.id">
                        <img :src="release.thumbnail.path + '.' + release.thumbnail.extension" :alt="release.title">
                        <router-link :to="{ name: 'comic', params: { id: release.id } }" class="release-name">{{
                            release.title }}</router-link>
                    </div>
                </div>
            </section>
        </div>

        <Footer />
    </div>
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
                newestReleases: [],
                isLoggedIn: false,
                username: null
            };
        },
        created() {
            this.loadCachedData();
            this.fetchDataWithBackoff('popularCharacters', this.fetchPopularCharacters);
            this.fetchDataWithBackoff('newestReleases', this.fetchNewestReleases);
            this.checkLoginStatus();
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
                            const waitTime = retries * 2000; // Verhoog de wachttijd
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
            checkLoginStatus() {
                const accessToken = localStorage.getItem('accessToken');
                if (accessToken) {
                    this.isLoggedIn = true;
                    return;
                }

                axios.get('/user')
                    .then(response => {
                        if (response.data) {
                            this.isLoggedIn = true;
                            this.username = response.data.username;
                        } else {
                            this.isLoggedIn = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error checking login status:', error);
                    });
            },
            selectComic(comic) {
                this.$router.push({ name: 'comic-details', params: { id: comic.id } });
            }
        }
    };
</script>

<style scoped>
    .main-content {
        display: flex;
        justify-content: center;
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
        margin-top: 5%;
        margin-bottom: 5%;
    }

    .greeting p {
        max-width: 65%;
        margin-left: 20%;
        margin-top: 3%;
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
        font-weight: bold
    }

    .characters-grid h2 {
        grid-column: span 2;
        margin: 0px;
        margin-top: -2%;
        margin-bottom: 2%;
        font-size: 20px;
    }

    .all-characters {
        width: 9%;
        display: block;
        text-align: right;
        margin-top: 10px;
        background-color: #d4a017;
        color: #fff;
        padding: 10px 8px;
        border-radius: 25px;
        text-decoration: none;
        margin-left: 89%;
        margin-bottom: 0.5%;
    }

    .all-releases {
        width: 8%;
        display: block;
        text-align: center;
        background-color: #d4a017;
        color: #fff;
        padding: 10px 4px;
        border-radius: 25px;
        text-decoration: none;
        margin-left: 90%;
        margin-top: -2%;
    }

    .newest-releases {
        margin-top: 70px;
    }

    .newest-releases h2 {
        text-align: center;
        font-size: 20px;
        margin-bottom: 1px;
    }

    .releases-carousel {
        display: flex;
        justify-content: unset;
        margin-top: 10px;
    }

    .release {
        flex: 0 0 auto;
        margin-left: 29.5px;
        margin-right: 75.3px;
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

    .comic-cards {
        max-width: 1536px;
        margin: 0 auto;
        padding: 20px;
    }

    h1 {
        font-size: 2rem;
        margin-bottom: 20px;
    }

    h2 {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .about {
        margin-top: 50px;
        padding: 20px;
    }

    .about-title {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    .about-content {
        display: flex;
        align-items: center;
    }

    .about-content-left {
        flex: 0 0 30%;
    }

    .about-logo {
        width: 100%;
        max-width: 200px;
        display: block;
        margin: 0 auto;
    }

    .about-content-right {
        max-width: 53%;
        flex: 1;
        padding: 20px;
    }

    .about-description {
        font-size: 18px;
        line-height: 1.5;
        color: #555;
    }

    .comic-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .comic-card {
        background-color: #f0f0f0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .comic-card:hover {
        transform: scale(1.05);
    }

    .comic-image {
        width: 100%;
        height: 79%;
    }

    .comic-details {
        padding: 20px;
    }

    .comic-title {
        margin-top: 0;
    }

    .username {
        font-size: 1.2rem;
        margin-bottom: 20px;
    }

    .contact {
        display: flex;
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .contact-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .contact-title {
        color: #333;
        font-size: 24px;
        margin-bottom: 10px;
    }

    .contact-info ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .contact-form {
        margin-top: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form-group textarea {
        resize: vertical;
    }

    button[type="submit"] {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }

    .contact-image {
        margin-left: 10px;
        flex: 0 0 30%;
        margin-top: 8%;
    }
</style>