<template>
    <div>
        <Header />
        <div class="container">
            <router-link to="/characters" class="back-button">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
                    <path class="arrow-icon" d="M15 7L10 12L15 17" stroke="#ffffff" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </router-link>
            <div class="character-details" v-if="character && character.thumbnail && character.series">
                <div class="character-detail-image-container">
                    <img :src="character.thumbnail.path + '.' + character.thumbnail.extension" :alt="character.name"
                        class="character-image">
                    <div class="circular-heart" @click="toggleWishlist(character)"
                        :class="{ 'wishlist-added': isInWishlist(character) }">
                        <i class="ri-heart-line icon"></i>
                    </div>
                </div>
                <div class="character-info">
                    <h2 class="title">{{ character.name }}</h2>
                    <div class="details">
                        <p><strong>Beschrijving:</strong> {{ character.description ||
                            generateDescription(character.name) }}
                        </p>
                    </div>
                </div>
            </div>
            <div v-else>
                <p>Loading...</p>
            </div>

            <section v-if="relatedComics && relatedComics.length > 0" class="related-comics-section">
                <div class="related-head">
                    <h3 class="small-title">Related comics</h3>
                    <router-link v-if="relatedComics.length > 5"
                        :to="'/serie/' + (character.series.items[0].resourceURI.split('/').pop())"
                        class="view-all-link">
                        View all related comics
                    </router-link>
                </div>
                <div class="comic-list">
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
                        <router-link :to="{ name: 'comic', params: { id: relatedComic.id } }" class="comic-title">
                            {{ relatedComic.title }}
                        </router-link>
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
                <p v-else>Er zijn nog geen reviews voor dit character.</p>
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
                character: {
                    series: {
                        items: []
                    }
                },
                error: null,
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
        created() {
            console.log('Character ID:', this.id);
            this.fetchCharacterDetails();
        },
        methods: {
            async fetchCharacterDetails() {
                try {
                    const response = await axios.get(`https://gateway.marvel.com/v1/public/characters/${this.id}?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471`);
                    if (response.data && response.data.data && response.data.data.results.length > 0) {
                        const characterData = response.data.data.results[0];
                        characterData.series = characterData.series || { items: [] }; // Ensure series is defined
                        this.character = characterData;
                        this.fetchRelatedComics(); // Fetch related comics
                    } else {
                        this.error = 'Character niet gevonden.';
                    }
                } catch (error) {
                    console.error(error);
                    this.error = 'Er is een fout opgetreden bij het ophalen van het character.';
                }
            },
            async fetchRelatedComics() {
                try {
                    const response = await axios.get(`https://gateway.marvel.com/v1/public/characters/${this.id}/comics?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471`);
                    if (response.data && response.data.data && response.data.data.results.length > 0) {
                        this.relatedComics = response.data.data.results;
                    }
                } catch (error) {
                    console.error(error);
                }
            },
            toggleWishlist(comic) {
                const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                const index = wishlist.findIndex(item => item.id === comic.id);
                if (index !== -1) {
                    wishlist.splice(index, 1); // Remove from wishlist
                    this.popupMessage = 'Comic verwijderd uit je wishlist';
                } else {
                    wishlist.push(comic); // Add to wishlist
                    this.popupMessage = 'Comic toegevoegd aan je wishlist';
                }
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                this.showPopup = true;
                setTimeout(() => {
                    this.showPopup = false;
                }, 3000); // Hide after 3 seconds
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
            },
            generateDescription(name) {
                return `Dit is een beschrijving van het character genaamd ${name}. Het is een spannend en boeiend character dat je zeker niet wilt missen!`;
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

    .title,
    .small-title,
    .smaller-title {
        font-size: 2em;
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

    .character-details {
        display: flex;
        flex-wrap: wrap;
        margin: -15px -18px;
        padding: 20px;
        position: relative;
    }

    .details {
        max-width: 450px;
    }

    .character-detail-image-container {
        position: relative;
        width: 300px;
        margin-right: 20px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .character-image {
        width: 300px;
        height: fit-content;
        margin-right: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    .character-info {
        flex: 1;
        margin-top: -13px;
    }

    p {
        margin: 10px 0px;
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

    .comic-list {
        display: flex;
        gap: 20px;
        justify-content: space-between;
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

    /* Media Queries for Responsive Design */
    @media (max-width: 1200px) {
        .character-details {
            flex-direction: column;
            align-items: center;
        }

        .character-detail-image-container,
        .character-image {
            width: 100%;
            max-width: 300px;
        }

        .character-info {
            margin-top: 20px;
            text-align: center;
        }
    }

    @media (max-width: 768px) {
        .related-comics-img-container {
            width: 100px;
            height: 150px;
        }

        .comic-title {
            font-size: 0.9em;
        }

        .view-all-link {
            font-size: 0.9em;
            padding: 8px 16px;
        }

        .back-button {
            width: 40px;
            height: 40px;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 0 10px;
        }

        .title,
        .small-title,
        .smaller-title {
            font-size: 1.5em;
        }

        .related-comics-section,
        .reviews,
        .private-notes-section {
            padding: 15px;
        }

        .comic-list {
            gap: 10px;
        }

        .view-all-link {
            padding: 6px 12px;
        }

        .back-button {
            width: 30px;
            height: 30px;
        }

        .back-button:hover {
            background-color: #CA8A04;
        }

        .edit-btn,
        .btn {
            padding: 5px;
            margin-left: 5px;
        }

        .sent-btn {
            padding: 8px 16px;
        }

        .reviews form {
            align-items: stretch;
        }
    }
</style>