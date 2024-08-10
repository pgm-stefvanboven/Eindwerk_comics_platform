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
                        <h2>Purchased Comics</h2>
                        <div v-if="collections.length" class="comic-grid">
                            <div v-for="item in collections" :key="item.id" class="comic-card">
                                <img v-if="item.comic.thumbnail" :src="`/storage/${item.comic.thumbnail}`"
                                    alt="Comic thumbnail" class="comic-thumbnail">
                                <div class="comic-info">
                                    <h3>{{ item.comic.title }}</h3>
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
                            <p>No comics in your collection yet.</p>
                        </div>
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

    export default {
        components: {
            Header,
            Footer
        },
        data() {
            return {
                collections: []
            };
        },
        mounted() {
            this.fetchCollections();
        },
        methods: {
            fetchCollections() {
                axios.get('/api/collections')
                    .then(response => {
                        this.collections = response.data;
                    })
                    .catch(error => {
                        console.error("Error fetching collections:", error);
                    });
            }
        }
    };
</script>

<style scoped>
    section.collection {
        padding: 30px 20px;
        max-width: 1536px;
        margin: 0 auto;
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
        color: #666;
    }

    .comic-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .comic-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #333;
    }

    .comic-details p {
        margin: 5px 0;
        color: #666;
    }

    .comic-details p strong {
        color: #333;
    }
</style>