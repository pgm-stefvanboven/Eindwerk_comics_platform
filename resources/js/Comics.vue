<template>
    <div>
        <Header />
        <section class="comics">
            <div class="titles">
                <h2>Filters</h2>
                <h2 class="comics-title">Newest Releases</h2>
            </div>
            <div class="content">
                <div class="filters">
                    <div class="filter">
                        <label for="characters">Characters</label>
                        <v-select id="characters" v-model="filters.characters" :options="characters" label="name"
                            :reduce="character => character.id" :multiple="true" />
                    </div>
                    <div class="filter">
                        <label for="series">Series</label>
                        <v-select id="series" v-model="filters.series" :options="seriesList" label="title"
                            :reduce="series => series.id" :multiple="true" />
                    </div>
                    <div class="filter">
                        <label for="writers">Writers</label>
                        <v-select id="writers" v-model="filters.writers" :options="writers" label="fullName"
                            :reduce="writer => writer.id" :multiple="true" />
                    </div>
                </div>

                <div class="comic-list">
                    <div v-if="isLoading">
                        <p>Loading...</p>
                    </div>
                    <div v-else-if="isLoadingFilteredComics">
                        <p>Loading...</p>
                    </div>
                    <div v-else-if="filteredComics.length === 0">No comics found</div>
                    <div v-for="comic in paginatedComics" :key="comic.id" class="comic-card">
                        <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title">
                        <router-link :to="{ name: 'comic', params: { id: comic.id } }" class="comic-title">{{
                            comic.title }}</router-link>
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
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

export default {
    components: {
        vSelect,
        Header,
        Footer
    },
    data() {
        return {
            comics: [],
            isLoading: true,
            isLoadingFilteredComics: false,
            filteredComics: [],
            characters: [],
            seriesList: [],
            writers: [],
            currentPage: 1,
            comicsPerPage: 10,
            filters: {
                characters: [],
                series: [],
                writers: []
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
            axios.get('https://gateway.marvel.com/v1/public/comics?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471')
                .then(response => {
                    this.comics = response.data.data.results.filter(comic => comic.thumbnail && comic.thumbnail.path !== 'image_not_found');
                    this.filteredComics = this.comics;
                    this.isLoading = false;
                })
                .catch(error => {
                    console.error('Error fetching comics:', error);
                });
        },
        fetchCharacters() {
            axios.get('https://gateway.marvel.com/v1/public/characters?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471')
                .then(response => {
                    this.characters = response.data.data.results.filter(character => character.comics.available > 0);
                })
                .catch(error => {
                    console.error('Error fetching characters:', error);
                });
        },
        fetchSeries() {
            axios.get('https://gateway.marvel.com/v1/public/series?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471')
                .then(response => {
                    this.seriesList = response.data.data.results;
                })
                .catch(error => {
                    console.error('Error fetching series:', error);
                });
        },
        fetchWriters() {
            axios.get('https://gateway.marvel.com/v1/public/creators?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471')
                .then(response => {
                    this.writers = response.data.data.results.filter(writer => writer.comics.available > 0);
                })
                .catch(error => {
                    console.error('Error fetching writers:', error);
                });
        },
        filterComics() {
            this.isLoadingFilteredComics = true; // Zet de nieuwe variabele op true aan het begin van de filterfunctie
            this.filteredComics = this.comics.filter(comic => {
                const matchesCharacters = this.filters.characters.length === 0 || comic.characters.items.some(character =>
                    this.filters.characters.includes(character.resourceURI.split('/').pop())
                );
                const matchesSeries = this.filters.series.length === 0 || this.filters.series.includes(comic.series.resourceURI.split('/').pop());
                const matchesWriters = this.filters.writers.length === 0 || comic.creators.items.some(creator =>
                    this.filters.writers.includes(creator.resourceURI.split('/').pop())
                );
                return matchesCharacters && matchesSeries && matchesWriters;
            });

            if (this.filteredComics.length === 0) {
                // Fetch comics for each character, series, and writer individually if no comics are found in the initial filter
                if (this.filters.characters.length > 0) {
                    this.filters.characters.forEach(characterId => {
                        this.fetchComicsByCharacter(characterId);
                    });
                }
                if (this.filters.series.length > 0) {
                    this.filters.series.forEach(seriesId => {
                        this.fetchComicsBySeries(seriesId);
                    });
                }
                if (this.filters.writers.length > 0) {
                    this.filters.writers.forEach(creatorId => {
                        this.fetchComicsByWriter(creatorId);
                    });
                }
            } else {
                this.isLoadingFilteredComics = false; // Zet de nieuwe variabele op false als er strips zijn gevonden
            }
        },
        fetchComicsByCharacter(characterId) {
            axios.get(`https://gateway.marvel.com/v1/public/characters/${characterId}/comics?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471`)
                .then(response => {
                    this.filteredComics = this.filteredComics.concat(response.data.data.results.filter(comic => comic.thumbnail && comic.thumbnail.path !== 'image_not_found'));
                    this.isLoadingFilteredComics = false; // Zet de nieuwe variabele op false na het laden van de strips
                })
                .catch(error => {
                    console.error('Error fetching comics by character:', error);
                });
        },
        fetchComicsBySeries(seriesId) {
            axios.get(`https://gateway.marvel.com/v1/public/series/${seriesId}/comics?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471`)
                .then(response => {
                    this.filteredComics = this.filteredComics.concat(response.data.data.results.filter(comic => comic.thumbnail && comic.thumbnail.path !== 'image_not_found'));
                    this.isLoadingFilteredComics = false; // Zet de nieuwe variabele op false na het laden van de strips
                })
                .catch(error => {
                    console.error('Error fetching comics by series:', error);
                });
        },
        fetchComicsByWriter(creatorId) {
            axios.get(`https://gateway.marvel.com/v1/public/creators/${creatorId}/comics?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471`)
                .then(response => {
                    this.filteredComics = this.filteredComics.concat(response.data.data.results.filter(comic => comic.thumbnail && comic.thumbnail.path !== 'image_not_found'));
                    this.isLoadingFilteredComics = false; // Zet de nieuwe variabele op false na het laden van de strips
                })
                .catch(error => {
                    console.error('Error fetching comics by writer:', error);
                });
        },
        nextPage() {
            if (this.currentPage * this.comicsPerPage < this.filteredComics.length) {
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
            return this.filteredComics.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.filteredComics.length / this.comicsPerPage);
        }
    },
    watch: {
        'filters.characters': {
            handler() {
                this.filterComics();
                this.currentPage = 1;
            },
            deep: true
        },
        'filters.series': {
            handler() {
                this.filterComics();
                this.currentPage = 1;
            },
            deep: true
        },
        'filters.writers': {
            handler() {
                this.filterComics();
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

.titles {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.titles h2 {
    font-size: 2em;
    font-weight: bold;
}

.comics-title {
    margin-right: 840px;
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
    margin-right: 460px;
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