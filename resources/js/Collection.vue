<template>
    <div>
        <Header />
        <section class="collection">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1>My Collection</h1>
                        <p class="collection-intro-text">
                            Welcome to your collection page! Here are the comics you've acquired.
                        </p>

                        <!-- Purchased Comics Section -->
                        <h2>Purchased Comics</h2>
                        <div v-if="purchasedComics.length" class="comic-grid">
                            <div v-for="item in purchasedComics" :key="item.id" class="comic-card">
                                <img v-if="item.comic.thumbnail" :src="`/storage/${item.comic.thumbnail}`"
                                    alt="Comic thumbnail" class="comic-thumbnail">
                                <div class="comic-info">
                                    <router-link :to="{ name: 'collectionDetail', params: { id: item.comic.id } }">
                                        <h3>{{ item.comic.title }}</h3>
                                    </router-link>
                                    <div class="comic-details">
                                        <p><strong>ID:</strong> {{ item.comic.id }}</p>
                                        <p><strong>Publisher:</strong> {{ item.comic.publisher }}</p>
                                        <p>{{ item.comic.description }}</p>
                                        <p><strong>Status:</strong> Purchased</p>
                                        <p><strong>Price:</strong> {{ item.comic.price }}</p>
                                        <p><strong>Poster:</strong> {{ item.comic.poster }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p>No purchased comics in your collection yet.</p>
                        </div>
                        <Pagination :total="totalPurchased" :current="currentPurchasedPage" :perPage="perPage"
                            @page-changed="fetchPurchasedComics">
                        </Pagination>

                        <!-- Rented Comics Section -->
                        <h2>Rented Comics</h2>
                        <div v-if="rentedComics.length" class="comic-grid">
                            <div v-for="item in rentedComics" :key="item.id" class="comic-card">
                                <img v-if="item.comic.thumbnail" :src="`/storage/${item.comic.thumbnail}`"
                                    alt="Comic thumbnail" class="comic-thumbnail">
                                <div class="comic-info">
                                    <router-link :to="{ name: 'collectionDetail', params: { id: item.comic.id } }">
                                        <h3>{{ item.comic.title }}</h3>
                                    </router-link>
                                    <div class="comic-details">
                                        <p><strong>ID:</strong> {{ item.comic.id }}</p>
                                        <p><strong>Publisher:</strong> {{ item.comic.publisher }}</p>
                                        <p>{{ item.comic.description }}</p>
                                        <p><strong>Status:</strong> Rented</p>
                                        <p><strong>Rent Expiry Date:</strong>
                                            {{ new Date(item.rental_end_date).toLocaleDateString() }}
                                        </p>
                                        <p><strong>Rent Price:</strong> {{ item.comic.price }}</p>
                                        <p><strong>Poster:</strong> {{ item.comic.poster }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p>No rented comics in your collection yet.</p>
                        </div>
                        <Pagination :total="totalRented" :current="currentRentedPage" :perPage="perPage"
                            @page-changed="fetchRentedComics">
                        </Pagination>

                    </div>
                </div>
            </div>
        </section>
        <Footer />
    </div>
</template>

<script>
    import axios from 'axios';
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';
    import Pagination from './vue-components/Pagination.vue';

    export default {
        components: {
            Header,
            Footer,
            Pagination
        },
        data() {
            return {
                purchasedComics: [],
                rentedComics: [],
                totalPurchased: 0,
                totalRented: 0,
                currentPurchasedPage: 1,
                currentRentedPage: 1,
                perPage: 8
            };
        },
        mounted() {
            this.fetchPurchasedComics(this.currentPurchasedPage);
            this.fetchRentedComics(this.currentRentedPage);
        },
        methods: {
            fetchPurchasedComics(page = 1) {
                axios.get(`/api/collections/purchased?page=${page}&perPage=${this.perPage}`)
                    .then(response => {
                        this.purchasedComics = response.data.data;
                        this.totalPurchased = response.data.total;
                        this.currentPurchasedPage = page;
                    })
                    .catch(error => {
                        console.error("Error fetching purchased comics:", error);
                    });
            },
            fetchRentedComics(page = 1) {
                axios.get(`/api/collections/rented?page=${page}&perPage=${this.perPage}`)
                    .then(response => {
                        this.rentedComics = response.data.data;
                        this.totalRented = response.data.total;
                        this.currentRentedPage = page;
                    })
                    .catch(error => {
                        console.error("Error fetching rented comics:", error);
                    });
            }
        }
    };
</script>

<style scoped>
    .collection {
        padding: 100px 0;
        text-align: center;
    }

    .collection h1 {
        font-size: 2.5rem;
        margin-bottom: 20px;
    }

    .collection-intro-text {
        font-size: 1.2rem;
        margin-bottom: 40px;
    }

    .comic-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .comic-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.2s;
    }

    .comic-card:hover {
        transform: translateY(-5px);
    }

    .comic-thumbnail {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }

    .comic-info {
        padding: 15px;
        text-align: left;
    }

    .comic-info h3 {
        margin-top: 0;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .comic-info p {
        margin: 5px 0;
    }

    h2 {
        font-size: 2rem;
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }
</style>