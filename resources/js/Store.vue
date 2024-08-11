<template>
    <div>
        <Header />
        <section class="collection">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1>Store</h1>
                        <p class="collection-intro-text">
                            Welcome to the store page! Here you can add your comics you want to trade or sell, rate them
                            and search for specific comics. Enjoy it! 🦸‍♂ It's time to complete your collection!️
                        </p>
                        <button @click="showForm = !showForm" class="btn btn-secondary">
                            {{ showForm ? 'Hide Form' : 'Add Comic' }}
                        </button>
                        <form v-if="showForm" @submit.prevent="addComic" class="add-comic-form">
                            <div class="form-group">
                                <label for="poster">Poster</label>
                                <input v-model="newComic.poster" placeholder="Poster" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" v-model="newComic.title" class="form-control" id="title" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea v-model="newComic.description" class="form-control" id="description"
                                    required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="total">Total comics</label>
                                <input v-model="newComic.total" type="number" placeholder="Total comics"
                                    class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="price">Price</label>
                                <input v-model="newComic.price" type="number" placeholder="Price" class="form-control"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="type">Type</label>
                                <select v-model="newComic.type" class="form-control" required>
                                    <option value="sale">For Sale</option>
                                    <option value="rent">For Rent</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="publisher">Publisher</label>
                                <input type="text" v-model="newComic.publisher" class="form-control" id="publisher"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="thumbnail">Thumbnail</label>
                                <input type="file" @change="handleFileUpload" class="form-control" id="thumbnail"
                                    required>
                            </div>

                            <button type="submit" class="btn btn-primary newcomic">Add Comic</button>
                        </form>

                        <input type="text" v-model="searchQuery" @input="fetchCollections(1)"
                            placeholder="Search comics...">

                        <h2>Comics for Sale</h2>
                        <div v-if="comicsForSale.length" class="comic-grid">
                            <div v-for="comic in comicsForSale" :key="comic.id" class="comic-card">
                                <img v-if="comic.thumbnail" :src="`/storage/${comic.thumbnail}`" alt="Comic thumbnail"
                                    class="comic-thumbnail">
                                <div class="comic-info">
                                    <h3>{{ comic.title }}</h3>
                                    <div class="comic-import-info">
                                        <p><strong>ID:</strong> {{ comic.id }}</p>
                                        <p><strong>Publisher:</strong> {{ comic.publisher }}</p>
                                        <p>{{ comic.description }}</p>
                                        <p>
                                            <strong>Total comics: </strong>
                                            <span v-if="comic.total > 0">{{ comic.total }}</span>
                                            <span v-else>Sold Out</span>
                                        </p>
                                        <p><strong>Price:</strong> {{ comic.price }}</p>
                                        <p><strong>Type:</strong> For {{ comic.type }}</p>
                                        <p><strong>Poster:</strong> {{ comic.poster }}</p>
                                    </div>

                                    <div v-if="comic.total > 0">
                                        <div class="comic-rating">
                                            <span>
                                                Rating: {{ Number(comic.rating ?? 0).toFixed(1) }} ({{
                                                comic.rating_count ?? 0 }} votes)
                                            </span>
                                            <select v-model="comic.newRating" @change="updateRating(comic)">
                                                <option disabled value="">Rate this comic</option>
                                                <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                                            </select>
                                        </div>
                                        <button @click="requestSwap(comic)" class="btn btn-info"
                                            :disabled="comic.swapInProgress">Request Swap</button>
                                    </div>
                                    <div v-else>
                                        <button class="btn btn-secondary" disabled>Sold Out</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p>No comics for sale available.</p>
                        </div>
                        <pagination :total="totalSale" :current="currentPageSale" :perPage="perPage"
                            @page-changed="page => fetchCollections(page, 'sale')" />

                        <h2>Comics for Rent</h2>
                        <div v-if="comicsForRent.length" class="comic-grid">
                            <div v-for="comic in comicsForRent" :key="comic.id" class="comic-card">
                                <img v-if="comic.thumbnail" :src="`/storage/${comic.thumbnail}`" alt="Comic thumbnail"
                                    class="comic-thumbnail">
                                <div class="comic-info">
                                    <h3>{{ comic.title }}</h3>
                                    <div class="comic-import-info">
                                        <p><strong>ID:</strong> {{ comic.id }}</p>
                                        <p><strong>Publisher:</strong> {{ comic.publisher }}</p>
                                        <p>{{ comic.description }}</p>
                                        <p>
                                            <strong>Total comics: </strong>
                                            <span v-if="comic.total > 0">{{ comic.total }}</span>
                                            <span v-else>Not Available</span>
                                        </p>
                                        <p><strong>Price:</strong> {{ comic.price }}</p>
                                        <p><strong>Type:</strong> For {{ comic.type }}</p>
                                        <p><strong>Poster:</strong> {{ comic.poster }}</p>
                                    </div>

                                    <div v-if="comic.total > 0">
                                        <div class="comic-rating">
                                            <span>
                                                Rating: {{ Number(comic.rating ?? 0).toFixed(1) }} ({{
                                                comic.rating_count ?? 0 }} votes)
                                            </span>
                                            <select v-model="comic.newRating" @change="updateRating(comic)">
                                                <option disabled value="">Rate this comic</option>
                                                <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                                            </select>
                                        </div>
                                        <button @click="requestSwap(comic)" class="btn btn-info"
                                            :disabled="comic.swapInProgress">Request Swap</button>
                                    </div>
                                    <div v-else>
                                        <button class="btn btn-secondary" disabled>Not Available</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p class="import-message">No comics for rent available.</p>
                        </div>

                        <pagination :total="totalRent" :current="currentPageRent" :perPage="perPage"
                            @page-changed="page => fetchCollections(page, 'rent')" />
                    </div>
                </div>
            </div>
        </section>
        <Footer />
        <SwapRequestsModal v-if="showSwapRequests" :requests="swapRequests" @close="showSwapRequests = false"
            @update="fetchSwapRequests" />
    </div>
</template>

<script>
    import axios from 'axios';
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';
    import Pagination from './vue-components/Pagination.vue';
    import SwapRequestsModal from './vue-components/SwapRequestsModal.vue';

    export default {
        components: {
            Header,
            Footer,
            Pagination,
            SwapRequestsModal
        },
        data() {
            return {
                comicsForSale: [],
                comicsForRent: [],
                newComic: {
                    poster: '',
                    title: '',
                    publisher: '',
                    description: '',
                    total: '',
                    price: '',
                    type: '',
                    thumbnail: null
                },
                searchQuery: '',
                currentPageSale: 1,
                currentPageRent: 1,
                totalSale: 0,
                totalRent: 0,
                perPage: 8,
                showForm: false,
                showSwapRequests: false,
                swapRequests: []
            };
        },
        mounted() {
            this.fetchCollections(1);
            this.fetchSwapRequests();
        },
        methods: {
            fetchCollections(page = 1, type = null) {
                const salePage = type === 'sale' || !type ? page : this.currentPageSale;
                const rentPage = type === 'rent' || !type ? page : this.currentPageRent;

                axios.get(`/api/stores?search=${this.searchQuery}&perPage=${this.perPage}&salePage=${salePage}&rentPage=${rentPage}`)
                    .then(response => {
                        this.comicsForSale = response.data.sale.data;
                        this.comicsForRent = response.data.rent.data;
                        this.totalSale = response.data.sale.total;
                        this.totalRent = response.data.rent.total;
                        this.currentPageSale = response.data.sale.current_page;
                        this.currentPageRent = response.data.rent.current_page;
                    })
                    .catch(error => {
                        console.error("Error fetching collections:", error);
                    });
            },
            handleFileUpload(event) {
                const file = event.target.files[0];
                this.newComic.thumbnail = file;
            },
            addComic() {
                const formData = new FormData();
                formData.append('poster', this.newComic.poster);
                formData.append('title', this.newComic.title);
                formData.append('publisher', this.newComic.publisher);
                formData.append('description', this.newComic.description);
                formData.append('total', this.newComic.total);
                formData.append('price', this.newComic.price);
                formData.append('type', this.newComic.type);
                formData.append('thumbnail', this.newComic.thumbnail);

                axios.post('/api/stores', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                    .then(response => {
                        const newComic = response.data;
                        if (newComic.type === 'sale') {
                            this.comicsForSale.unshift(newComic);
                            this.totalSale++;
                        } else if (newComic.type === 'rent') {
                            this.comicsForRent.unshift(newComic);
                            this.totalRent++;
                        }

                        this.resetForm();
                    })
                    .catch(error => {
                        console.error("Error adding comic:", error);
                    });
            },
            resetForm() {
                this.newComic = {
                    poster: '',
                    title: '',
                    publisher: '',
                    description: '',
                    total: '',
                    price: '',
                    type: '',
                    thumbnail: null
                };
                this.showForm = false;
            },
            updateRating(comic) {
                if (!comic.newRating) {
                    console.error("No rating selected");
                    return;
                }
                axios.patch(`/api/stores/${comic.id}/rating`, { rating: comic.newRating })
                    .then(response => {
                        comic.rating = response.data.rating;
                        comic.rating_count = response.data.rating_count;
                        comic.newRating = '';
                    })
                    .catch(error => {
                        console.error("Error updating rating:", error);
                    });
            },
            async requestSwap(comic) {
                try {
                    if (!this.swapRequests) {
                        await this.fetchSwapRequests(); // Ensure swap requests are loaded
                    }

                    const selectedComicId = prompt('Enter the ID of the comic you want to offer in exchange:');

                    if (!selectedComicId) return;

                    comic.swapInProgress = true;

                    const response = await axios.post('/api/swaps', {
                        comic_id: selectedComicId,
                        requested_comic_id: comic.id
                    });

                    if (response.data.success) {
                        alert('Swap request sent!');
                        comic.total = response.data.updated_comic.total;

                        if (comic.total <= 0) {
                            comic.soldOut = true;
                        }

                        // Update swap requests synchronously
                        this.fetchSwapRequests();
                    } else {
                        alert('Swap request sent! Reload the page to see the changes');
                    }

                } catch (error) {
                    console.error("Error requesting swap:", error);
                    alert('Error sending swap request');
                } finally {
                    comic.swapInProgress = false;
                }
            },
            fetchSwapRequests() {
                axios.get('/api/swaps')
                    .then(response => {
                        this.swapRequests = response.data;
                    })
                    .catch(error => {
                        console.error("Error fetching swap requests:", error);
                    });
            }
        }
    }
</script>

<style scoped>
    section.collection {
        padding: 30px 0;
        max-width: 1536px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .collection h1 {
        font-size: 2.5rem;
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .collection h2 {
        font-size: 2rem;
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .collection-intro-text {
        max-width: 600px;
        font-size: 1.25rem;
        text-align: center;
        margin: 0 auto 20px;
        margin-bottom: 20px;
        color: #666;
    }

    .collection .btn-secondary {
        display: block;
        margin: 0 auto 20px;
        background-color: #6c757d;
        color: #fff;
        padding: 10px 20px;
        font-size: 1rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .collection .btn-secondary:hover {
        background-color: #5a6268;
    }

    .add-comic-form {
        max-width: 600px;
        margin: 0 auto 20px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .add-comic-form .form-group {
        margin-bottom: 15px;
    }

    .add-comic-form .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #555;
    }

    .add-comic-form .form-control {
        width: 100%;
        padding: 10px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .add-comic-form .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

    .add-comic-form .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .collection input[type="text"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
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
        height: auto;
        display: block;
    }

    .comic-info {
        padding: 15px;
    }

    .comic-info h3 {
        margin-top: 0;
        font-size: 1.5rem;
        color: #333;
    }

    .comic-import-info p {
        margin: 5px 0;
        color: #555;
    }

    .comic-rating {
        margin-top: 10px;
    }

    .comic-rating span {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
    }

    .comic-rating select {
        width: 100%;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .btn-info {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #17a2b8;
        border: none;
        border-radius: 4px;
        color: #fff;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-info:hover {
        background-color: #138496;
    }

    .collection .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        margin-bottom: 138px;
    }

    .collection .pagination button {
        background: #007bff;
        color: #fff;
        border: none;
        padding: 10px 15px;
        margin: 0 5px;
        border-radius: 4px;
        cursor: pointer;
    }

    .collection .pagination button:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }

    .collection .pagination button:not(:disabled):hover {
        background: #0056b3;
    }

    .collection .pagination span {
        margin: 0 10px;
        color: #333;
        font-size: 1rem;
    }

    .collection .pagination input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 50px;
        text-align: center;
    }

    .collection .pagination input:focus {
        outline: none;
    }

    .collection .btn-info {
        background-color: #17a2b8;
        color: #fff;
        padding: 10px 20px;
        font-size: 1rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

    .collection .btn-info:hover {
        background-color: #138496;
    }

    .collection .swap-requests-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .newcomic {
        margin-top: 10px;
    }

    .import-message {
        text-align: center;
        color: #666;
    }
</style>