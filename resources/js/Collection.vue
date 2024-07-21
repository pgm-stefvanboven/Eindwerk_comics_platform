<template>
    <div>
        <Header />
        <section class="collection">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h1>Collection</h1>
                        <p class="collection-intro-text">
                            Welcome to the collection page! Here you can add your comics you want to trade, rate them
                            and search for specific comics. Enjoy it! 🦸‍♂ complete your collection!️
                        </p>
                        <button @click="showForm = !showForm" class="btn btn-secondary">
                            {{ showForm ? 'Hide Form' : 'Add Comic' }}
                        </button>
                        <form v-if="showForm" @submit.prevent="addComic" class="add-comic-form">
                            <!-- Form fields here -->
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" v-model="newComic.title" class="form-control" id="title" required>
                                <label for="description">Description</label>
                                <textarea v-model="newComic.description" class="form-control" id="description"
                                    required></textarea>
                                <label for="publisher">Publisher</label>
                                <input type="text" v-model="newComic.publisher" class="form-control" id="publisher"
                                    required>
                                <label for="thumbnail">Thumbnail</label>
                                <input type="file" @change="handleFileUpload" class="form-control" id="thumbnail"
                                    required>
                                <button type="submit" class="btn btn-primary newcomic">Add Comic</button>
                            </div>
                        </form>
                        <input type="text" v-model="searchQuery" @input="fetchCollections"
                            placeholder="Search comics...">
                        <div v-if="collections.length" class="comic-grid">
                            <div v-for="comic in collections" :key="comic.id" class="comic-card">
                                <img v-if="comic.thumbnail" :src="`/storage/${comic.thumbnail}`" alt="Comic thumbnail"
                                    class="comic-thumbnail">
                                <div class="comic-info">
                                    <h3>{{ comic.title }}</h3>
                                    <p><strong>ID:</strong> {{ comic.id }}</p>
                                    <p><strong>Publisher:</strong> {{ comic.publisher }}</p>
                                    <p>{{ comic.description }}</p>
                                    <div class="comic-rating">
                                        <span>Rating: {{ (comic.rating ?? 0).toFixed(1) }} ({{ comic.rating_count ?? 0
                                            }} votes)</span>
                                        <select v-model="comic.newRating" @change="updateRating(comic)">
                                            <option disabled value="">Rate this comic</option>
                                            <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                                        </select>
                                    </div>
                                    <button @click="requestSwap(comic)" class="btn btn-info">Request Swap</button>
                                </div>
                            </div>
                        </div>
                        <pagination :total="total" :current="currentPage" @page-changed="fetchCollections"></pagination>
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
                collections: [],
                newComic: {
                    title: '',
                    publisher: '',
                    description: '',
                    thumbnail: null
                },
                searchQuery: '',
                currentPage: 1,
                total: 0,
                showForm: false,
                showSwapRequests: false,
                swapRequests: []
            }
        },
        mounted() {
            this.fetchCollections();
            this.fetchSwapRequests();
        },
        methods: {
            fetchCollections(page = 1) {
                axios.get(`/api/collections?page=${page}&search=${this.searchQuery}`)
                    .then(response => {
                        this.collections = response.data.data.map(comic => ({
                            ...comic,
                            rating: Number(comic.rating) || 0,
                            rating_count: comic.rating_count ?? 0
                        }));
                        this.total = response.data.total;
                        this.currentPage = response.data.current_page;
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
                formData.append('title', this.newComic.title);
                formData.append('publisher', this.newComic.publisher);
                formData.append('description', this.newComic.description);
                formData.append('thumbnail', this.newComic.thumbnail);

                axios.post('/api/collections', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                    .then(response => {
                        this.collections.push(response.data);
                        this.newComic.title = '';
                        this.newComic.publisher = '';
                        this.newComic.description = '';
                        this.newComic.thumbnail = null;
                        this.showForm = false;
                    })
                    .catch(error => {
                        console.error("Error adding comic:", error);
                    });
            },
            updateRating(comic) {
                if (!comic.newRating) {
                    console.error("No rating selected");
                    return;
                }
                axios.patch(`/api/collections/${comic.id}/rating`, { rating: comic.newRating })
                    .then(response => {
                        comic.rating = response.data.rating;
                        comic.rating_count = response.data.rating_count;
                        comic.newRating = '';
                    })
                    .catch(error => {
                        console.error("Error updating rating:", error);
                    });
            },
            requestSwap(comic) {
                // Ask the user to enter the comic ID they want to offer in return
                const selectedComicId = prompt('Enter the ID of the comic you want to offer in exchange:');

                axios.post('/api/swaps', {
                    comic_id: selectedComicId,
                    requested_comic_id: comic.id
                })
                    .then(response => {
                        alert('Swap request sent!');
                        this.fetchSwapRequests();
                    })
                    .catch(error => {
                        console.error("Error requesting swap:", error);
                    });
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

    .collection .form-group {
        margin-bottom: 15px;
    }

    .collection .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #555;
    }

    .collection .form-control {
        width: 100%;
        padding: 10px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .collection .btn-primary {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        font-size: 1rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .collection .btn-primary:hover {
        background-color: #0056b3;
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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .comic-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
        font-size: 1.25rem;
        margin-bottom: 10px;
        color: #333;
    }

    .comic-info p {
        margin: 0 0 10px;
        color: #666;
    }

    .comic-rating {
        margin-top: 10px;
    }

    .comic-rating span {
        display: block;
        margin-bottom: 5px;
        color: #666;
    }

    .comic-rating select {
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
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
</style>