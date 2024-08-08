<template>
    <div>
        <Header />
        <div class="list-container">
            <h1>Listings</h1>
            <!-- Button to show/hide the form -->
            <button class="toggle-form-button" @click="toggleForm">
                {{ showForm ? 'Hide Form' : 'Add New Comic' }}
            </button>

            <!-- Form is only shown if showForm is true -->
            <form v-if="showForm" @submit.prevent="addListing" enctype="multipart/form-data">
                <input v-model="newListing.poster" placeholder="Poster" required>
                <input v-model="newListing.title" placeholder="Title" required>
                <textarea v-model="newListing.description" placeholder="Description" required></textarea>
                <input type="file" @change="onFileChange" required>
                <input v-model="newListing.price" type="number" placeholder="Price">
                <select v-model="newListing.type" required>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                </select>
                <button type="submit">Add Comic</button>
            </form>
            <div class="listing">
                <div v-for="listing in listings" :key="listing.id" class="listing-card">
                    <router-link :to="'/listing/' + listing.id">
                        <h2>{{ listing.title }}</h2>
                        <img :src="listing.image" alt="Comic Image">
                    </router-link>
                    <p class="description">{{ listing.description }}</p>
                    <p>Price: € {{ listing.price }}</p>
                    <p>Type: {{ listing.type }}</p>
                    <p class="posted">Posted by:
                    <p class="clickable-poster" @click="openChat(listing.poster, listing)">{{ listing.poster }}</p>
                    </p>
                </div>
            </div>
        </div>
        <Footer />
        <div v-if="showChat" class="chat-window">
            <ChatComponent :poster="selectedPoster" :listings="listings" :comic="selectedComic" @close="closeChat" />
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
                showForm: false, // Variable to show/hide the form
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
                    })
                    .catch(error => {
                        if (error.response.status === 429) {
                            console.error('Te veel verzoeken, probeer later opnieuw.');
                        } else {
                            console.error('Error fetching listings:', error);
                        }
                    });
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
                })
                    .then(response => {
                        this.listings.push(response.data);
                        this.newListing = {
                            poster: '',
                            title: '',
                            description: '',
                            image: null,
                            price: '',
                            type: 'sale'
                        };
                        this.showForm = false; // Hide form after submission
                    });
            },
            onFileChange(event) {
                this.newListing.image = event.target.files[0];
            },
            toggleForm() {
                this.showForm = !this.showForm; // Toggle form visibility
            },
            openChat(poster, comic) {
                this.selectedPoster = poster;
                this.selectedComic = comic; // Ensure this is set
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
        margin: 2em auto;
        font-family: Arial, sans-serif;
    }

    .list-container h1 {
        text-align: center;
        color: #333;
        font-size: 2em;
        margin-bottom: 1em;
        position: relative;
    }

    .list-container h1::after {
        content: "";
        display: block;
        width: 50px;
        height: 4px;
        background: #007BFF;
        margin: 0.5em auto;
    }

    .toggle-form-button {
        display: block;
        margin: 0 auto 1.5em auto;
        padding: 0.75em 2em;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1em;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .toggle-form-button:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    .toggle-form-button:active {
        background-color: #0056b3;
        transform: scale(1);
    }

    .list-container form {
        display: flex;
        flex-direction: column;
        margin-bottom: 2em;
        padding: 1em;
        border: 1px solid #ddd;
        border-radius: 10px;
        background: #f9f9f9;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .list-container form input,
    .list-container form textarea,
    .list-container form select {
        margin-bottom: 1em;
        padding: 0.75em;
        font-size: 1em;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .list-container form button {
        padding: 0.75em 1.5em;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .list-container form button:hover {
        background-color: #0056b3;
    }

    .listing {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5em;
        justify-content: space-between;
    }

    .listing-card {
        width: calc(33.333% - 1em);
        border: 1px solid #ddd;
        margin-bottom: 2em;
        padding: 1em;
        box-sizing: border-box;
        border-radius: 10px;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .listing-card h2 {
        color: #007BFF;
        cursor: pointer;
        font-size: 1.5em;
        margin-bottom: 0.5em;
    }

    .listing-card img {
        width: 100%;
        height: auto;
        margin-bottom: 0.5em;
        border-radius: 10px;
    }

    .description {
        font-size: 1em;
        color: #666;
        margin-bottom: 0.5em;
    }

    .posted {
        display: flex;
        align-items: center;
        font-size: 1em;
        color: #666;
    }

    .clickable-poster {
        cursor: pointer;
        color: #007BFF;
        margin-left: 0.2em;
    }

    .clickable-poster:hover {
        color: #666;
    }

    .chat-window {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 320px;
        height: 480px;
        border: 1px solid #ccc;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }
</style>