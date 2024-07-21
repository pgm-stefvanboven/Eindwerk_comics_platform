<template>
    <div>
        <Header />
        <div class="list-detail-container">
            <h1>{{ listing.title }}</h1>
            <img :src="listing.image" alt="Comic Image" class="comic-image">
            <p class="description">{{ listing.description }}</p>
            <div class="info">
                <h2>
                    Comic info:
                </h2>
                <p>Price: € {{ listing.price }}</p>
                <p>Type: for {{ listing.type }}</p>
                <p>Poster: {{ listing.poster }}</p>
            </div>

            <div class="reviews-section">
                <h2>Reviews:</h2>
                <div v-for="review in listing.reviews" :key="review.id" class="review">
                    <p>{{ review.review }}</p>
                    <p>Rating: {{ review.rating }}</p>
                </div>

                <button @click="toggleReviewForm" class="toggle-button">
                    {{ showReviewForm ? 'Hide Review Form' : 'Add a Review' }}
                </button>

                <form v-if="showReviewForm" @submit.prevent="addReview" class="review-form">
                    <input v-model="reviewUser" placeholder="Your Name" required>
                    <textarea v-model="reviewText" placeholder="Your Review" required></textarea>
                    <input type="number" v-model="rating" min="1" max="5" required>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
        <Footer />
    </div>
</template>


<script>
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';
    import axios from 'axios';

    export default {
        components: {
            Header,
            Footer
        },
        props: ['id'],
        data() {
            return {
                listing: null,
                reviewUser: '',
                reviewText: '',
                rating: null,
                showReviewForm: false
            };
        },
        created() {
            this.fetchListing();
        },
        methods: {
            fetchListing() {
                axios.get(`/api/listings/${this.id}`)
                    .then(response => {
                        this.listing = response.data;
                    });
            },
            toggleReviewForm() {
                this.showReviewForm = !this.showReviewForm;
            },
            addReview() {
                axios.post('/api/reviews', {
                    user: this.reviewUser,
                    listing_id: this.listing.id,
                    rating: this.rating,
                    review: this.reviewText
                }).then(() => {
                    this.reviewUser = '';
                    this.reviewText = '';
                    this.rating = null;
                    this.fetchListing();
                });
            }
        }
    };
</script>

<style scoped>
    .list-detail-container {
        width: 100%;
        margin: auto;
        font-family: Arial, sans-serif;
        padding: 2em;
    }

    .list-detail-container h1 {
        text-align: center;
        color: #333;
        margin-bottom: 1em;
    }

    .comic-image {
        display: block;
        max-width: 50%;
        height: auto;
        margin: 0 auto 1em auto;
        border-radius: 5px;
    }

    .description {
        font-size: 1.2em;
        margin-bottom: 1em;
        text-align: center;
    }

    .info {
        text-align: center;
        font-size: 1.1em;
        margin-bottom: 1em;
    }

    .info h2 {
        color: #007BFF;
        margin-bottom: 0.5em;
    }

    .info p {
        margin: 0.5em 0;
    }

    .reviews-section {
        text-align: center;
        font-size: 1.1em;
        margin-bottom: 1em;
    }

    .reviews-section h2 {
        color: #007BFF;
        margin-bottom: 0.5em;
    }

    .toggle-button {
        padding: 0.5em 1em;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-bottom: 1em;
        display: block;
        margin: 0 auto;
    }

    .toggle-button:hover {
        background-color: #0056b3;
    }

    .review-form {
        display: flex;
        flex-direction: column;
        margin-top: 1em;
        padding: 1em;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .review-form input,
    .review-form textarea {
        margin-bottom: 1em;
        padding: 0.5em;
        font-size: 1em;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .review-form button {
        padding: 0.5em 1em;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .review-form button:hover {
        background-color: #0056b3;
    }

    .review {
        background-color: #fff;
        padding: 1em;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 1em;
    }

    .review p {
        margin: 0.5em 0;
    }
</style>