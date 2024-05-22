<template>
    <div>
        <Header />
        <section class="comics">
            <div class="content">
                <div class="filters">
                    <div class="filter">
                        <label for="characters">Characters</label>
                        <select id="characters" v-model="filters.character">
                            <option v-for="character in characters" :key="character.id" :value="character.name">{{
                                character.name }}</option>
                        </select>
                    </div>

                    <div class="filter">
                        <label for="series">Series</label>
                        <select id="series" v-model="filters.serie">
                            <option v-for="serie in series" :key="serie.id" :value="serie.title">{{ serie.title }}
                            </option>
                        </select>
                    </div>

                    <div class="filter">
                        <label for="writers">Writers</label>
                        <select id="writers" v-model="filters.writer">
                            <option v-for="creator in creators" :key="creator.id" :value="creator.firstName">{{
                                creator.firstName }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="comic-list">
                    <div v-for="comic in paginatedComics" :key="comic.id" class="comic-card">
                        <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title">
                        <p class="comic-title">{{ comic.title }}</p>
                    </div>
                </div>
            </div>

            <div class="pagination">
                <button v-if="currentPage > 1" @click="previousPage">Vorige</button>
                <button v-if="currentPage < totalPages" @click="nextPage" class="next">Volgende</button>
                <span class="pages">Pagina {{ currentPage }} van {{ totalPages }}</span>
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
            comics: [],
            characters: [],
            currentPage: 1,
            comicsPerPage: 10,
            filters: {
                character: '',
                serie: '',
                writer: ''
            }
        };
    },
    created() {
        this.fetchComics();
        this.fetchCharacters();
        this.fetchSeries();
        this.fetchWriters();
    },
    methods: {
        fetchComics() {
            axios.get('https://gateway.marvel.com/v1/public/comics?ts=1&apikey=9446f8eb6e1702835dbb961d763f4401&hash=b1f7a0387b6770554c0768bb48ac02c1')
                .then(response => {
                    this.comics = response.data.data.results.filter(comic => comic.thumbnail && comic.thumbnail.path !== 'image_not_found');
                })
                .catch(error => {
                    console.error('Error fetching comics:', error);
                });
        },
        fetchCharacters() {
            axios.get('https://gateway.marvel.com/v1/public/characters?ts=1&apikey=9446f8eb6e1702835dbb961d763f4401&hash=b1f7a0387b6770554c0768bb48ac02c1')
                .then(response => {
                    this.characters = response.data.data.results;
                })
                .catch(error => {
                    console.error('Error fetching characters:', error);
                });
        },
        fetchSeries() {
            axios.get('https://gateway.marvel.com/v1/public/series?ts=1&apikey=9446f8eb6e1702835dbb961d763f4401&hash=b1f7a0387b6770554c0768bb48ac02c1')
                .then(response => {
                    this.series = response.data.data.results;
                })
                .catch(error => {
                    console.error('Error fetching series:', error);
                });
        },
        fetchWriters() {
            axios.get('https://gateway.marvel.com/v1/public/creators?ts=1&apikey=9446f8eb6e1702835dbb961d763f4401&hash=b1f7a0387b6770554c0768bb48ac02c1')
                .then(response => {
                    this.creators = response.data.data.results;
                })
                .catch(error => {
                    console.error('Error fetching Writers:', error);
                });
        },
        nextPage() {
            if (this.currentPage * this.comicsPerPage < this.comics.length) {
                this.currentPage++;
            }
        },
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        }
    },
    computed: {
        paginatedComics() {
            const start = (this.currentPage - 1) * this.comicsPerPage;
            const end = start + this.comicsPerPage;
            return this.comics.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.comics.length / this.comicsPerPage);
        }
    },
    watch: {
        filters: {
            handler() {
                this.currentPage = 1;
            },
            deep: true
        }
    }
};
</script>

<style scoped>
.comics {
    max-width: 1536px;
    margin: 0 auto;
    padding: 20px;
}

.content {
    display: flex;
    justify-content: space-between;
}

.comic-list {
    flex: 3;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    grid-gap: 20px;
}

.comic-list img {
    width: 100%;
    height: 70%;
}

.comic-title {
    margin-top: 10px;
    font-size: 1.2em;
    text-align: center;
}

.filters {
    flex: 1;
}

.filters {
    display: flex;
    flex-direction: column;
    gap: 60px;
    margin-right: 30px;
}

.filter {
    display: flex;
    flex-direction: column;
}

.filter label {
    font-weight: bold;
    margin-bottom: 5px;
}

.filter select {
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
    margin-right: 362px;
}

button {
    background-color: #CA8A04;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    transition-duration: 0.4s;
    border-radius: 25px;
}

.next,
.pages {
    margin-left: 10px;
}

button:hover {
    background-color: #FFD700;
}

@media (max-width: 768px) {
    .comic-list {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
}

@media (max-width: 480px) {
    .comic-list {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
}

@media (max-width: 320px) {
    .comic-list {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }
}
</style>