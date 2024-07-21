<template>
    <div>
        <Header />
        <div class="list-container">
            <h1>Listings</h1>
            <form @submit.prevent="addListing" enctype="multipart/form-data">
                <input v-model="newListing.poster" placeholder="Poster" required>
                <input v-model="newListing.title" placeholder="Title" required>
                <textarea v-model="newListing.description" placeholder="Description" required></textarea>
                <input type="file" @change="onFileChange" required>
                <input v-model="newListing.price" type="number" placeholder="Price">
                <select v-model="newListing.type" required>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                </select>
                <button type="submit">Add Listing</button>
            </form>
            <div class="listing">
                <div v-for="listing in listings" :key="listing.id" class="listing-card">
                    <router-link :to="{ name: 'ListingDetails', params: { id: listing.id } }">
                        <h2>{{ listing.title }}</h2>
                    </router-link>
                    <router-link :to="{ name: 'ListingDetails', params: { id: listing.id } }">
                        <img :src="listing.image" alt="Comic Image">
                    </router-link>
                    <p class="description">{{ listing.description }}</p>
                    <p>Price: € {{ listing.price }}</p>
                    <p>Type: for {{ listing.type }}</p>
                    <p @click="openChat(listing.poster)" class="clickable-poster">Poster: {{ listing.poster }}</p>
                </div>
            </div>
        </div>
        <Footer />
        <div v-if="showChat" class="chat-window">
            <chat-component :poster="selectedPoster" @close="closeChat"></chat-component>
        </div>
    </div>
</template>

<script>
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';
    import ChatComponent from './vue-components/ChatComponent.vue';
    import axios from 'axios';

    export default {
        components: {
            Header,
            Footer,
            ChatComponent
        },
        data() {
            return {
                listings: [],
                newListing: {
                    poster: '',
                    title: '',
                    description: '',
                    image: null,
                    price: '',
                    type: 'sale'
                },
                showChat: false,
                selectedPoster: ''
            };
        },
        created() {
            this.fetchListings();
        },
        methods: {
            fetchListings() {
                axios.get('/api/listings')
                    .then(response => {
                        this.listings = response.data;
                    });
            },
            onFileChange(event) {
                this.newListing.image = event.target.files[0];
            },
            addListing() {
                const formData = new FormData();
                formData.append('poster', this.newListing.poster);
                formData.append('title', this.newListing.title);
                formData.append('description', this.newListing.description);
                formData.append('image', this.newListing.image);
                formData.append('price', this.newListing.price);
                formData.append('type', this.newListing.type);

                axios.post('/api/listings', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    this.newListing = {
                        poster: '',
                        title: '',
                        description: '',
                        image: null,
                        price: '',
                        type: 'sale'
                    };
                    // Haal de lijst opnieuw op
                    this.fetchListings();
                });
            },
            openChat(poster) {
                this.selectedPoster = poster;
                this.showChat = true;
            },
            closeChat() {
                this.showChat = false;
            }
        }
    };
</script>

<style>
    .list-container {
        width: 80%;
        margin: auto;
        font-family: Arial, sans-serif;
    }

    .list-container h1 {
        text-align: center;
        color: #333;
    }

    .list-container form {
        display: flex;
        flex-direction: column;
        margin-bottom: 2em;
    }

    .list-container form input,
    .list-container form textarea,
    .list-container form select {
        margin-bottom: 1em;
        padding: 0.5em;
        font-size: 1em;
    }

    .list-container form button {
        padding: 0.5em 1em;
        background-color: #007BFF;
        color: white;
        border: none;
        cursor: pointer;
    }

    .listing {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .listing-card {
        width: calc(33.333% - 1em);
        border: 1px solid #ddd;
        margin-bottom: 2em;
        padding: 1em;
        box-sizing: border-box;
    }

    .listing-card h2 {
        color: #007BFF;
    }

    .listing-card p {
        margin: 0.5em 0;
    }

    .listing-card img {
        width: 100%;
        height: auto;
    }

    .clickable-poster {
        cursor: pointer;
    }

    .clickable-poster:hover {
        color: #007BFF;
    }

    .chat-window {
        position: fixed;
        bottom: 0;
        right: 0;
        width: 300px;
        height: 400px;
        border: 1px solid #ccc;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
</style>