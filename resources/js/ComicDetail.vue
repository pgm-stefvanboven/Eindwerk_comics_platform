<template>
    <div>
        <Header />
        <div class="container">
            <router-link to="/comics" class="back-button">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
                    <path class="arrow-icon" d="M15 7L10 12L15 17" stroke="#ffffff" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </router-link>
            <div class="comic-details" v-if="comic">
                <div class="comic-image-container">
                    <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.name" class="comic-image">
                    <div class="circular-heart" @click="toggleWishlist(comic)"
                        :class="{ 'wishlist-added': isInWishlist(comic) }">
                        <i class="ri-heart-line icon"></i>
                    </div>
                </div>
                <div class="comic-info">
                    <h2 class="title">{{ comic.title }}</h2>
                    <h3 class="sm-title">More Details:</h3>
                    <div class="details">
                        <p v-if="comic.format"><strong>Formaat:</strong> {{ comic.format }}</p>
                        <p v-if="comic.series"><strong>Serie:</strong> {{ comic.series.name }}</p>
                        <p><strong>Prijs:</strong> €{{ comicPrice }}</p>
                        <p v-if="comic.dates && comic.dates.length > 0"><strong>Uitgegeven op:</strong> {{ randomDate }}
                        </p>
                        <p><strong>Aantal pagina's:</strong> {{ comicPageCount }}</p>
                        <p v-if="comic.creators && comic.creators.items.length > 0">
                            <strong>Gemaakt door: </strong>
                            <span v-for="(creator, index) in comic.creators.items" :key="index">
                                {{ creator.name }}{{ index < comic.creators.items.length - 1 ? ', ' : '' }} </span>
                        </p>
                        <p><strong>Beschrijving:</strong> {{ comic.description || generateDescription(comic.title) }}
                        </p>
                    </div>
                </div>
            </div>
            <div v-else>
                <p>Loading...</p>
            </div>

            <section class="private-notes-section">
                <h3 class="small-title">Private Note</h3>
                <div v-if="privateNote && !isEditingPrivateNote">
                    <p>{{ privateNote }}</p>
                    <div class="btns">
                        <button @click="editPrivateNote" class="edit-btn"><i class="ri-pencil-fill"></i> Bewerk
                            notitie</button>
                        <button @click="deletePrivateNote" class="btn"><i class="ri-delete-bin-6-fill"></i> Verwijder
                            notitie</button>
                    </div>
                </div>
                <p v-else>Er is nog geen privénotitie voor deze comic.</p>
                <form v-if="!privateNote || isEditingPrivateNote" @submit.prevent="submitPrivateNote"
                    class="private-note-form">
                    <textarea v-model="newPrivateNote" rows="4" cols="50"></textarea>
                    <div class="btns" v-if="newPrivateNote.trim().length > 0">
                        <button type="submit" class="sent-btn">{{ isEditingPrivateNote ? 'Opslaan' : 'Voeg toe'
                            }}</button>
                        <button type="button" @click="cancelPrivateNoteEdit" class="btn">Annuleren</button>
                    </div>
                </form>
            </section>

            <section v-if="relatedComics.length > 0" class="related-comics-section">
                <h3 class="small-title">Meer van: {{ comic.series.name }}</h3>
                <div class="comic-list">
                    <div v-for="(relatedComic, index) in relatedComics.slice(0, 5)" :key="relatedComic.id"
                        class="comic-card">
                        <div class="img-container">
                            <div class="circular-heart" @click="toggleWishlist(relatedComic)"
                                :class="{ 'wishlist-added': isInWishlist(relatedComic) }">
                                <i class="ri-heart-line icon"></i>
                            </div>
                            <router-link :to="{ name: 'comic', params: { id: relatedComic.id } }">
                                <img :src="`${relatedComic.thumbnail.path}.${relatedComic.thumbnail.extension}`"
                                    :alt="relatedComic.title" />
                            </router-link>
                        </div>
                        <router-link :to="{ name: 'comic', params: { id: relatedComic.id } }" class="comic-title">{{
                            relatedComic.title }}</router-link>
                    </div>
                    <router-link v-if="relatedComics.length > 5"
                        :to="'/series/' + comic.series.resourceURI.split('/').pop()" class="view-all-link">Bekijk alle
                        gerelateerde comics</router-link>
                </div>
            </section>

            <section class="reviews">
                <h3 class="small-title">Reviews</h3>
                <div v-if="reviews.length > 0" class="review-list">
                    <div v-for="(review, index) in reviews" :key="index" class="review-item">
                        <p>{{ review.text }}</p>
                        <div class="btns">
                            <button @click="editReview(index)" class="btn"><i class="ri-pencil-fill"></i></button>
                            <button @click="deleteReview(index)" class="btn"><i
                                    class="ri-delete-bin-6-fill"></i></button>
                        </div>
                    </div>
                </div>
                <p v-else>Er zijn nog geen reviews voor deze comic.</p>
                <h4 class="smaller-title">{{ isEditing ? 'Bewerk je review' : 'Voeg een review toe' }}</h4>
                <form @submit.prevent="submitReview">
                    <textarea id="review" v-model="newReview" rows="4" cols="50"></textarea>
                    <div class="btns">
                        <button type="submit" v-if="newReview">{{ isEditing ? 'Opslaan' : 'Verstuur' }}</button>
                        <button type="button" v-if="isEditing" @click="cancelEdit" class="btn">Annuleren</button>
                    </div>
                </form>
            </section>
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
                reviews: [],
                newReview: '',
                isEditing: false,
                editIndex: -1,
                showPopup: false,
                privateNote: '',
                newPrivateNote: '',
                isEditingPrivateNote: false
            };
        },
        computed: {
            comicPrice() {
                if (this.comic && this.comic.prices && this.comic.prices.length > 0 && this.comic.prices[0].price > 0) {
                    return this.comic.prices[0].price.toFixed(2);
                } else {
                    let price = localStorage.getItem('comicPrice');
                    if (!price) {
                        price = this.generateRandomPrice().toFixed(2);
                        localStorage.setItem('comicPrice', price);
                    }
                    return price;
                }
            },
            comicPageCount() {
                if (this.comic && this.comic.pageCount > 0) {
                    return this.comic.pageCount;
                } else {
                    let pageCount = localStorage.getItem('pageCount');
                    if (!pageCount) {
                        pageCount = this.generateRandomPageCount();
                        localStorage.setItem('pageCount', pageCount);
                    }
                    return pageCount;
                }
            }
        },
        created() {
            console.log('Comic ID:', this.id);
            this.fetchComicDetails();
            this.loadReviews();
            this.loadRandomDate();
            this.loadPrivateNote();
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
                            .filter(comic => comic.id !== this.id); // Exclude current comic from related comics
                    } else {
                        this.relatedComics = [];
                    }
                } catch (error) {
                    console.error('Error fetching related comics:', error);
                    this.relatedComics = [];
                }
            },
            generateDescription(title) {
                return `Dit is een beschrijving van de comic getiteld "${title}". Meer informatie volgt binnenkort. 😉`;
            },
            generateRandomDate() {
                const startYear = 2003;
                const endYear = 2024;
                const start = new Date(startYear, 0, 1);
                const end = new Date(endYear, 11, 31);
                const randomDate = new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
                return randomDate.toLocaleDateString('nl-NL', { year: 'numeric', month: 'long', day: 'numeric' });
            },
            saveRandomDate(date) {
                localStorage.setItem(`comic-${this.id}-randomDate`, date);
            },
            loadRandomDate() {
                const savedDate = localStorage.getItem(`comic-${this.id}-randomDate`);
                if (savedDate) {
                    this.randomDate = savedDate;
                } else {
                    this.randomDate = this.generateRandomDate();
                    this.saveRandomDate(this.randomDate);
                }
            },
            generateRandomPrice() {
                let price = 0;
                while (price < 1) {
                    price = Math.random() * 15;
                }
                return price;
            },
            generateRandomPageCount() {
                return Math.floor(Math.random() * 500) + 1;
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
            },
            addReview() {
                if (this.newReview.trim()) {
                    this.reviews.push(this.newReview.trim());
                    localStorage.setItem(`comic-${this.id}-reviews`, JSON.stringify(this.reviews));
                    this.newReview = '';
                }
            },
            loadReviews() {
                const savedReviews = localStorage.getItem(`comic-${this.id}-reviews`);
                if (savedReviews) {
                    this.reviews = JSON.parse(savedReviews);
                }
            },
            submitReview() {
                if (this.newReview.trim()) {
                    if (this.isEditing) {
                        this.reviews.splice(this.editIndex, 1, { text: this.newReview });
                        this.isEditing = false;
                        this.editIndex = -1;
                    } else {
                        this.reviews.push({ text: this.newReview });
                    }
                    localStorage.setItem(`comic-${this.id}-reviews`, JSON.stringify(this.reviews));
                    this.newReview = '';
                }
            },
            editReview(index) {
                this.newReview = this.reviews[index].text;
                this.isEditing = true;
                this.editIndex = index;
            },
            deleteReview(index) {
                this.reviews.splice(index, 1);
                localStorage.setItem(`comic-${this.id}-reviews`, JSON.stringify(this.reviews));
            },
            cancelEdit() {
                this.newReview = '';
                this.isEditing = false;
                this.editIndex = -1;
            },
            loadPrivateNote() {
                const savedNote = localStorage.getItem(`comic-${this.id}-private-note`);
                if (savedNote) {
                    this.privateNote = savedNote;
                }
            },
            submitPrivateNote() {
                if (this.newPrivateNote.trim()) {
                    this.privateNote = this.newPrivateNote.trim();
                    localStorage.setItem(`comic-${this.id}-private-note`, this.privateNote);
                    this.isEditingPrivateNote = false;
                    this.newPrivateNote = '';
                }
            },
            editPrivateNote() {
                this.newPrivateNote = this.privateNote;
                this.isEditingPrivateNote = true;
            },
            deletePrivateNote() {
                this.privateNote = '';
                localStorage.removeItem(`comic-${this.id}-private-note`);
                this.newPrivateNote = '';
                this.isEditingPrivateNote = false;
            },
            cancelPrivateNoteEdit() {
                this.newPrivateNote = '';
                this.isEditingPrivateNote = false;
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

    .title {
        font-size: 2em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .sm-title {
        font-size: 1.5em;
        font-weight: bold;
    }

    .small-title,
    .smaller-title {
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .back-button {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #CA8A04;
        color: white;
        border-radius: 50px;
        margin: 10px 0;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 50px;
        height: 50px;
    }

    .back-button:hover {
        background-color: #E0A800;
    }

    .arrow-icon {
        transition: transform 0.3s;
        transform: translateX(-1px);
        stroke: white;
    }

    .comic-details {
        display: flex;
        flex-wrap: wrap;
        margin: -15px -18px;
        padding: 20px;
    }

    .comic-details {
        position: relative;
    }

    .details {
        max-width: 450px;
    }

    .comic-image-container {
        position: relative;
        width: 300px;
        height: 450px;
        margin-right: 20px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .comic-image {
        width: 300px;
        height: fit-content;
        margin-right: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    .comic-info {
        flex: 1;
        margin-top: -13px;
    }

    p {
        margin: 10px 0px;
    }

    .related-comics-section {
        margin: 40px 0;
    }

    .comic-list {
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

    .wishlist-added {
        background-color: #d63031;
    }

    .wishlist-added .icon {
        color: black;
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

    .reviews {
        margin: 40px 0;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
    }

    .review-list {
        margin-bottom: 20px;
    }

    .review-item {
        padding: 10px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btns {
        display: flex;
    }

    .edit-btn {
        background-color: #CA8A04;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        padding: 5px 10px;
    }

    .edit-btn:hover {
        background-color: #E0A800;
    }

    .sent-btn {
        padding: 10px 20px;
        background-color: #CA8A04;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn {
        margin-left: 10px;
        background-color: #CA8A04;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        padding: 5px 10px;
    }

    .sent-btn:hover,
    .btn:hover {
        background-color: #E0A800;
    }

    .reviews form {
        display: flex;
        flex-direction: column;
    }

    .reviews textarea {
        resize: none;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .reviews button {
        align-self: flex-start;
        padding: 10px 20px;
        background-color: #CA8A04;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .reviews button:hover {
        background-color: #E0A800;
    }

    .private-notes-section {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
    }

    .private-note-form {
        display: flex;
        flex-direction: column;
    }

    .private-note-form textarea {
        max-width: 100%;
        resize: vertical;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .private-note-form button {
        align-self: flex-start;
        padding: 10px 20px;
        background-color: #CA8A04;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .private-note-form button:hover {
        background-color: #E0A800;
    }

    textarea {
        flex-grow: 1;
    }
</style>