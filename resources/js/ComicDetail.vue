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
                    <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.name"
                        class="comic-image">
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

            <section v-if="relatedComics.length > 0" class="related-comics-section">
                <div class="related-head">
                    <h3 class="small-title">Meer van: {{ comic.series.name }}</h3>
                    <router-link v-if="relatedComics.length > 5"
                        :to="'/series/' + comic.series.resourceURI.split('/').pop()" class="view-all-link">
                        Bekijk alle gerelateerde comics
                    </router-link>
                </div>
                <div class="comic-detail-list">
                    <div v-for="(relatedComic, index) in relatedComics.slice(0, 5)" :key="relatedComic.id"
                        class="comic-card">
                        <div class="related-comics-img-container">
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
                </div>
            </section>

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
                        <button type="submit" class="sent-btn">
                            {{ isEditingPrivateNote ? 'Opslaan' : 'Voeg toe' }}
                        </button>
                        <button type="button" @click="cancelPrivateNoteEdit" class="btn">Annuleren</button>
                    </div>
                </form>
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

            <!-- Popup -->
            <div v-if="showPopup" class="popup">
                <p>{{ popupMessage }}</p>
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
                reviews: [],
                newReview: '',
                isEditing: false,
                editIndex: -1,
                showPopup: false,
                popupMessage: '',
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
            this.fetchComic();
            this.generateRandomDate();
        },
        methods: {
            async fetchComic() {
                try {
                    const response = await axios.get(`https://gateway.marvel.com/v1/public/comics/${this.id}?ts=1&apikey=82f244ed9c0f202819d3fe45dd4649ac&hash=75a2dd8424de9077cb1018bee9101da5`);
                    this.comic = response.data.data.results[0];
                    this.fetchRelatedComics();
                } catch (error) {
                    this.error = error.response ? error.response.data : error.message;
                    console.error("Failed to fetch comic:", this.error);
                }
            },
            generateRandomDate() {
                const randomDate = new Date(+(new Date()) - Math.floor(Math.random() * 10000000000));
                const dateString = randomDate.toISOString().split('T')[0];
                this.randomDate = dateString;
            },
            generateRandomPrice() {
                return (Math.random() * (30 - 3 + 1) + 3).toFixed(2);
            },
            generateRandomPageCount() {
                return Math.floor(Math.random() * (100 - 32 + 1) + 32);
            },
            generateDescription(title) {
                return `No description available for ${title}`;
            },
            async fetchRelatedComics() {
                try {
                    const seriesId = this.comic.series.resourceURI.split('/').pop();
                    const response = await axios.get(`https://gateway.marvel.com/v1/public/series/${seriesId}/comics?ts=1&apikey=82f244ed9c0f202819d3fe45dd4649ac&hash=75a2dd8424de9077cb1018bee9101da5`);
                    this.relatedComics = response.data.data.results.filter(comic => comic.id !== this.comic.id);
                } catch (error) {
                    this.error = error.response ? error.response.data : error.message;
                    console.error("Failed to fetch related comics:", this.error);
                }
            },
            toggleWishlist(comic) {
                const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                const index = wishlist.findIndex(item => item.id === comic.id);
                if (index !== -1) {
                    wishlist.splice(index, 1);
                    this.showPopupMessage(`${comic.title} is verwijderd uit je verlanglijst!`);
                } else {
                    wishlist.push(comic);
                    this.showPopupMessage(`${comic.title} is toegevoegd aan je verlanglijst!`);
                }
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
            },
            isInWishlist(comic) {
                const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                return wishlist.some(item => item.id === comic.id);
            },
            submitReview() {
                if (this.isEditing && this.editIndex !== -1) {
                    this.reviews.splice(this.editIndex, 1, { text: this.newReview });
                    this.showPopupMessage('Review bijgewerkt!');
                } else {
                    this.reviews.push({ text: this.newReview });
                    this.showPopupMessage('Review toegevoegd!');
                }
                this.newReview = '';
                this.isEditing = false;
                this.editIndex = -1;
                localStorage.setItem('reviews', JSON.stringify(this.reviews));
            },
            deleteReview(index) {
                this.reviews.splice(index, 1);
                this.showPopupMessage('Review verwijderd!');
                localStorage.setItem('reviews', JSON.stringify(this.reviews));
            },
            editReview(index) {
                this.newReview = this.reviews[index].text;
                this.isEditing = true;
                this.editIndex = index;
            },
            cancelEdit() {
                this.isEditing = false;
                this.newReview = '';
                this.editIndex = -1;
            },
            showPopupMessage(message) {
                this.popupMessage = message;
                this.showPopup = true;
                setTimeout(() => {
                    this.showPopup = false;
                }, 3000);
            },
            submitPrivateNote() {
                this.privateNote = this.newPrivateNote;
                this.showPopupMessage('Notitie toegevoegd!');
                this.newPrivateNote = '';
                this.isEditingPrivateNote = false;
                localStorage.setItem('privateNote', this.privateNote);
            },
            editPrivateNote() {
                this.newPrivateNote = this.privateNote;
                this.isEditingPrivateNote = true;
            },
            deletePrivateNote() {
                this.privateNote = '';
                this.showPopupMessage('Notitie verwijderd!');
                localStorage.removeItem('privateNote');
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
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .popup button {
        background-color: transparent;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 16px;
        margin-left: 20px;
    }

    .related-comics-section {
        margin: 40px 0;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 10px;
    }

    .related-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .comic-detail-list {
        display: flex;
        gap: 20px;
        justify-content: left;
        flex-wrap: wrap;
    }

    .comic-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        border-radius: 10px;
        padding: 10px;
        transition: transform 0.3s;
    }

    .comic-card:hover {
        transform: scale(1.05);
    }

    .related-comics-img-container {
        position: relative;
        width: 150px;
        height: 225px;
        margin-bottom: 10px;
        overflow: hidden;
        border-radius: 10px;
    }

    .related-comics-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .related-comics-img-container:hover img {
        transform: scale(1.1);
    }

    .comic-title {
        font-size: 1em;
        text-align: center;
        font-weight: bold;
        margin-top: 10px;
    }

    .view-all-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        font-size: 1em;
        font-weight: bold;
        color: #f9f9f9;
        background-color: #CA8A04;
        padding: 10px 20px;
        border-radius: 20px;
        text-decoration: none;
        transition: color 0.3s;
    }

    .view-all-link:hover {
        background-color: #E0A800;
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