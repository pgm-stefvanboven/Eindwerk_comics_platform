<template>
    <div>
        <Header />
        <router-link to="/comics" class="back-button">Terug naar overzicht</router-link>
        <div v-if="comic" class="comic-details">
            <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title" class="comic-image">
            <div class="comic-info">
                <h2>{{ comic.title }}</h2>
                <h3>More Details</h3>
                <p v-if="comic.format">Formaat: {{ comic.format }}</p>
                <p v-if="comic.pageCount">Aantal pagina's: {{ comic.pageCount }}</p>
                <p v-if="comic.modified">Published: {{ formatDate(comic.modified) }}</p>
                <p v-if="comic.creators && comic.creators.items.length > 0">Gemaakt door:
                    <span v-for="(creator, index) in comic.creators.items" :key="index">
                        {{ creator.name }}{{ index < comic.creators.items.length - 1 ? ', ' : '' }} </span>
                </p>
                <p v-if="comic.description">{{ comic.description }}</p>
                <p v-else>{{ generateDescription(comic.title) }}</p>
            </div>
        </div>

        <div v-else-if="error">
            <p>{{ error }}</p>
        </div>
        <div v-else>
            <p>Loading...</p>
        </div>

        <div v-if="relatedComics.length > 0" class="related-comics-section">
            <h3>Meer van {{ comic.series.name }}</h3>
            <div class="comic-list">
                <div v-for="(relatedComic, index) in relatedComics.slice(0, 5)" :key="relatedComic.id"
                    class="comic-card">
                    <div class="img-container">
                        <div class="circular-heart" @click="toggleWishlist(relatedComic)"
                            :class="{ 'wishlist-added': isInWishlist(relatedComic) }">
                            <i class="ri-heart-line icon"></i>
                        </div>
                        <router-link :to="{ name: 'comic', params: { id: relatedComic.id } }">
                            <img :src="relatedComic.thumbnail.path + '.' + relatedComic.thumbnail.extension"
                                :alt="relatedComic.title">
                        </router-link>
                    </div>
                    <router-link :to="{ name: 'comic', params: { id: relatedComic.id } }" class="comic-title">
                        {{ relatedComic.title }}
                    </router-link>
                </div>
                <router-link v-if="relatedComics.length > 5"
                    :to="'/series/' + comic.series.resourceURI.split('/').pop()" class="view-all-link">
                    Bekijk alle gerelateerde comics
                </router-link>
            </div>
        </div>
        <Footer />
    </div>
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
        props: ['id'],
        data() {
            return {
                comic: null,
                error: null,
                randomDate: null,
                relatedComics: [],
                showPopup: false // Add this line
            };
        },
        created() {
            console.log('Comic ID:', this.id);
            this.fetchComicDetails();
            this.loadRandomDate();
        },
        methods: {
            async fetchComicDetails() {
                try {
                    const response = await axios.get(`https://gateway.marvel.com/v1/public/comics/${this.id}?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471`);
                    if (response.data && response.data.data && response.data.data.results.length > 0) {
                        this.comic = response.data.data.results[0];
                        this.fetchRelatedComics(this.comic.series.resourceURI); // Haal gerelateerde comics op
                    } else {
                        this.error = 'Comic niet gevonden.';
                    }
                } catch (error) {
                    console.error('Error fetching comic details:', error);
                    this.error = 'Er is een fout opgetreden bij het ophalen van de comic gegevens.';
                }
            },
            async fetchRelatedComics(seriesURI) {
                try {
                    const seriesId = seriesURI.split('/').pop();
                    const response = await axios.get(`https://gateway.marvel.com/v1/public/series/${seriesId}/comics?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471`);
                    if (response.data && response.data.data && response.data.data.results.length > 0) {
                        this.relatedComics = response.data.data.results
                            .filter(comic => comic.id !== this.comic.id) // Exclude current comic
                            .sort((a, b) => new Date(a.dates.find(date => date.type === 'onsaleDate').date) - new Date(b.dates.find(date => date.type === 'onsaleDate').date)); // Sorteer op datum
                    }
                } catch (error) {
                    console.error('Error fetching related comics:', error);
                    this.error = 'Er is een fout opgetreden bij het ophalen van de gerelateerde comics.';
                }
            },
            generateDescription(title) {
                return `Dit is een beschrijving van de comic getiteld "${title}". Meer informatie volgt binnenkort.`;
            },
            formatDate(dateStr) {
                if (!dateStr) return this.getOrCreateRandomDate(this.comic.title);
                const date = new Date(dateStr);
                if (isNaN(date.getTime())) return this.getOrCreateRandomDate(this.comic.title);
                return date.toLocaleDateString('nl-NL', { year: 'numeric', month: 'long', day: 'numeric' });
            },
            getOrCreateRandomDate(title) {
                if (this.randomDate) return this.randomDate;
                this.randomDate = this.generateRandomDate(title);
                this.saveRandomDate(this.randomDate);
                return this.randomDate;
            },
            generateRandomDate(title) {
                const yearMatch = title.match(/\b(19|20)\d{2}\b/);
                let startDate, endDate;
                if (yearMatch) {
                    const year = parseInt(yearMatch[0]);
                    startDate = new Date(year, 0, 1);
                    endDate = new Date(year, 11, 31);
                } else {
                    startDate = new Date(2000, 0, 1);
                    endDate = new Date(2024, 11, 31);
                }
                const randomDate = new Date(startDate.getTime() + Math.random() * (endDate.getTime() - startDate.getTime()));
                return randomDate.toLocaleDateString('nl-NL', { year: 'numeric', month: 'long', day: 'numeric' });
            },
            saveRandomDate(date) {
                localStorage.setItem(`comic-${this.id}-randomDate`, date);
            },
            loadRandomDate() {
                const savedDate = localStorage.getItem(`comic-${this.id}-randomDate`);
                if (savedDate) {
                    this.randomDate = savedDate;
                }
            },
            toggleWishlist(comic) {
                const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                const index = wishlist.findIndex(item => item.id === comic.id);
                if (index !== -1) {
                    wishlist.splice(index, 1); // Remove from wishlist
                } else {
                    wishlist.push(comic); // Add to wishlist
                    this.showPopup = true; // Show the popup when a comic is added
                    setTimeout(() => {
                        this.showPopup = false;
                    }, 3000); // Hide the popup after 3 seconds
                }
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
            },
            isInWishlist(comic) {
                const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                return wishlist.some(item => item.id === comic.id);
            }
        }
    };
</script>

<style scoped>
    .back-button {
        display: inline-block;
        background-color: #CA8A04;
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        text-decoration: none;
        margin-top: 10px;
        margin-left: 40px;
        margin-bottom: -10px;
    }

    .back-button:hover {
        background-color: #E0A800;
    }

    .comic-details {
        display: flex;
        align-items: flex-start;
        margin: 20px;
        padding: 20px;
    }

    .comic-image {
        max-width: 300px;
        margin-right: 20px;
    }

    .comic-info {
        flex: 1;
    }

    h2 {
        margin-top: 0;
    }

    p {
        margin: 5px 0;
    }

    .related-comics-section {
        margin: 40px;
    }

    .comic-list {
        flex: 3;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }

    .comic-card {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .img-container {
        position: relative;
        width: 100%;
        padding-top: 150%;
        overflow: hidden;
        border-radius: 10px;
        transition: box-shadow 0.3s;
    }

    .img-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 10px;
        transition: transform 0.3s;
    }

    .img-container:hover img {
        transform: scale(1.05);
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

    .comic-title {
        margin-top: 10px;
        font-size: 1.2em;
        text-align: center;
        font-weight: bold;
    }

    .view-all-link {
        text-align: center;
        margin-top: 20px;
        font-size: 1em;
        font-weight: bold;
    }
</style>