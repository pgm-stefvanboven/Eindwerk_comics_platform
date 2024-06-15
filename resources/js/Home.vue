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
                    <a href="/wishlist" class="wishlist-button">Wishlist</a>
                </div>
                <div class="popular-characters">
                    <div class="characters-grid">
                        <h2 class="character-title">Featured Characters</h2>
                        <div class="character" v-for="character in popularCharacters" :key="character.id">
                            <router-link :to="{ name: 'character', params: { id: character.id } }">
                                <img :src="character.thumbnail.path + '.' + character.thumbnail.extension"
                                    :alt="character.name">
                            </router-link>
                            <router-link :to="{ name: 'character', params: { id: character.id } }"
                                class="populair-character-name">
                                {{ character.name }}
                            </router-link>
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
                            <router-link :to="{ name: 'comic', params: { id: release.id } }" class="release-name">
                                <img :src="release.thumbnail.path + '.' + release.thumbnail.extension"
                                    :alt="release.title">
                            </router-link>
                        </div>
                        <router-link :to="{ name: 'comic', params: { id: release.id } }" class="release-name">
                            {{ release.title }}
                        </router-link>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <Footer />
    <div v-if="showPopup" class="popup">
        <p class="popup-text">{{ popupMessage }}</p>
        <a href="/wishlist" class="button">Ga naar wishlist</a>
        <button @click="showPopup = false">Sluiten</button>
    </div>
</template>

<script>
    import axios from 'axios';
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';

    // Constants for API details
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
                comics: [], // Array to store comics data
                popularCharacters: [], // Array to store popular characters data
                newestReleases: [], // Array to store newest releases data
                showPopup: false, // Boolean to control popup visibility
                popupMessage: '' // String to store popup message
            };
        },
        created() {
            this.loadCachedData(); // Load cached data from local storage when the component is created
            this.fetchDataWithBackoff('popularCharacters', this.fetchPopularCharacters); // Fetch popular characters with backoff mechanism
            this.fetchDataWithBackoff('newestReleases', this.fetchNewestReleases); // Fetch newest releases with backoff mechanism
        },
        methods: {
            // Load cached data from local storage
            loadCachedData() {
                const cachedCharacters = localStorage.getItem('popularCharacters');
                if (cachedCharacters) {
                    this.popularCharacters = JSON.parse(cachedCharacters); // Load popular characters from local storage if available
                }
                const cachedReleases = localStorage.getItem('newestReleases');
                if (cachedReleases) {
                    this.newestReleases = JSON.parse(cachedReleases); // Load newest releases from local storage if available
                }
            },
            // Fetch data with exponential backoff in case of rate limiting (429 error)
            async fetchDataWithBackoff(dataType, fetchFunction) {
                if (this[dataType].length > 0) {
                    return; // If data is already loaded, return immediately
                }

                const maxRetries = 5; // Maximum number of retries
                let retries = 0;
                const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms)); // Helper function for delay

                while (retries < maxRetries) {
                    try {
                        await fetchFunction.call(this); // Call the fetch function
                        localStorage.setItem(dataType, JSON.stringify(this[dataType])); // Cache the fetched data
                        break; // Break the loop if fetching is successful
                    } catch (error) {
                        if (error.response && error.response.status === 429) {
                            retries++;
                            const waitTime = retries * 2000; // Exponential backoff wait time
                            console.error(`Attempt ${retries} failed. Retrying in ${waitTime}ms...`);
                            await delay(waitTime); // Wait before retrying
                        } else {
                            console.error(`Failed to fetch ${dataType}:`, error); // Log other errors and break the loop
                            break;
                        }
                    }
                }
            },
            // Fetch popular characters from the API
            async fetchPopularCharacters() {
                const response = await axios.get(`${API_URL}/characters?orderBy=-modified&limit=4&ts=${TIMESTAMP}&apikey=${API_KEY}&hash=${HASH}`);
                this.popularCharacters = response.data.data.results; // Store the fetched popular characters
            },
            // Fetch newest releases from the API
            async fetchNewestReleases() {
                const response = await axios.get(`${API_URL}/comics?orderBy=-onsaleDate&limit=5&ts=${TIMESTAMP}&apikey=${API_KEY}&hash=${HASH}`);
                this.newestReleases = response.data.data.results; // Store the fetched newest releases
                // Replace 'image_not_available' thumbnails with a default image
                this.newestReleases.forEach(release => {
                    if (release.thumbnail.path.endsWith('image_not_available')) {
                        release.thumbnail.path = '/images/clean';
                    }
                });
            },
            // Check if a release is in the wishlist
            isInWishlist(release) {
                let wishlist = JSON.parse(localStorage.getItem('wishlist')) || []; // Get the wishlist from local storage
                return wishlist.some(item => item.id === release.id); // Return true if the release is in the wishlist
            },
            // Add or remove a release from the wishlist
            toggleWishlist(release) {
                let wishlist = JSON.parse(localStorage.getItem('wishlist')) || []; // Get the wishlist from local storage
                const index = wishlist.findIndex(item => item.id === release.id); // Find the index of the release in the wishlist

                if (index !== -1) {
                    wishlist.splice(index, 1); // If the release is in the wishlist, remove it
                    this.popupMessage = `${release.title} verwijderd uit wishlist.`; // Set popup message for removal
                } else {
                    wishlist.push(release); // If the release is not in the wishlist, add it
                    this.popupMessage = `${release.title} toegevoegd aan wishlist.`; // Set popup message for addition
                }

                localStorage.setItem('wishlist', JSON.stringify(wishlist)); // Update the wishlist in local storage
                this.showPopup = true; // Show the popup message
            }
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
        height: 29rem;
        margin-left: 34%;
        background-color: #fdc247;
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
        width: 145px;
        height: 125px;
        margin-bottom: 10px;
        border-radius: 10px;
        cursor: pointer;
        transition: transform .3s;
    }


    .character img:hover {
        transform: scale(1.1);
        transition: transform 0.3s;
    }

    .populair-character-name {
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

    .release .img-container {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
        border-radius: 10px;
        transition: transform .3s;
    }

    .release .img-container img {
        transition: transform 0.2s;
    }

    .release:hover .img-container img {
        transform: scale(1.05);
    }

    .release-name {
        text-align: center;
        margin-top: 5px;
        font-size: 16px;
        font-weight: bold;
    }

    .circular-heart {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: transform 0.2s;
        z-index: 10;
    }

    .circular-heart:hover {
        transform: scale(1.1);
    }

    .wishlist-added .icon {
        color: black;
    }

    .wishlist-added {
        color: #d63031;
        background-color: #d63031;
    }

    .icon {
        font-size: 20px;
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

    .button {
        background-color: #CA8A04;
        border: none;
        color: white;
        padding: 5px 15px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        cursor: pointer;
        transition-duration: 0.4s;
        border-radius: 25px;
        margin-right: 10px;
    }

    .popup-text {
        margin-bottom: 10px;
    }

    .popup button {
        background-color: transparent;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 16px;
    }

    /* Big Tablets and smartphones in landscape */
    @media (max-width: 1024px) {
        .main-content {
            flex-direction: column;
            align-items: center;
        }

        .greeting,
        .popular-characters {
            width: 80%;
            margin: 10px 0;
        }

        .popular-characters {
            margin-left: 0;
        }
    }

    /* Smartphones */
    @media (max-width: 768px) {

        .greeting,
        .popular-characters {
            width: 100%;
            height: auto;
        }

        .greeting p {
            max-width: 100%;
        }

        .release {
            width: 150px;
            height: 225px;
        }

        .release img {
            object-fit: contain;
        }

        .all-releases,
        .all-characters {
            width: 100%;
        }

        .release-name {
            font-size: 14px;
        }

        .circular-heart {
            width: 25px;
            height: 25px;
        }

        .icon {
            font-size: 16px;
        }
    }

    /* Little smartphones */
    @media (max-width: 480px) {

        .intro-title,
        .title,
        .character-title {
            font-size: 1.5em;
        }

        .greeting,
        .popular-characters {
            padding: 10px;
        }

        .wishlist-button {
            padding: 5px 15px;
        }

        .button {
            padding: 5px 10px;
        }
    }
</style>