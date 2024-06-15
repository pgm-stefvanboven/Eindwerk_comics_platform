<template>
    <div>
        <Header />
        <section class="characters">
            <div class="titles">
                <h2>Filters</h2>
                <h2 class="characters-title">Featured Characters</h2>
            </div>
            <div class="content">
                <div class="filters">
                    <div class="filter">
                        <label for="series">Series</label>
                        <v-select id="series" v-model="filters.series" :options="filteredSeriesList" label="title"
                            :reduce="series => series.id" :multiple="true">
                            <template #no-options>
                                <p v-if="isLoadingSeries">Loading series...</p>
                                <p v-else>No matching options</p>
                            </template>
                        </v-select>
                    </div>
                </div>
                <div class="character-list">
                    <div v-if="isLoading">
                        <p>Loading...</p>
                    </div>
                    <div v-else-if="isLoadingFilteredCharacters">
                        <p>Loading...</p>
                    </div>
                    <div v-else-if="filteredCharacters.length === 0">
                        <p>No characters found</p>
                    </div>
                    <div v-for="character in paginatedCharacters" :key="character.id" class="character-card">
                        <img :src="character.thumbnail.path + '.' + character.thumbnail.extension" :alt="character.name"
                            class="character-image">
                        <router-link :to="{ name: 'character', params: { id: character.id } }" class="character-name">
                            {{ character.name }}
                        </router-link>
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
                characters: [], // Array to store all characters
                isLoading: true, // Boolean to indicate loading state for characters
                isLoadingFilteredCharacters: false, // Boolean to indicate loading state for filtered characters
                isLoadingSeries: true, // Boolean to indicate loading state for series
                filteredCharacters: [], // Array to store filtered characters
                seriesList: [], // Array to store all series
                filteredSeriesList: [], // Array to store series with characters
                currentPage: 1, // Current page number for pagination
                charactersPerPage: 10, // Number of characters to display per page
                filters: {
                    series: [] // Selected series for filtering characters
                },
                seriesBatchSize: 20, // Number of series to fetch per batch
                totalSeries: 100, // Total number of series to fetch
                showResetButton: false // Boolean to control the visibility of the reset button
            };
        },
        created() {
            this.fetchCharacters(); // Fetch all characters when the component is created
            this.fetchSeriesWithCharacters(); // Fetch all series with characters when the component is created
        },
        methods: {
            fetchCharacters() {
                axios.get('https://gateway.marvel.com/v1/public/characters?ts=1&apikey=8f852f8f9626b398d68a9b0c1d614afa&hash=586424658aeb66500b1f02216339670b')
                    .then(response => {
                        // Filter characters to exclude those with missing or "image_not_found" thumbnails
                        this.characters = response.data.data.results.filter(character => character.thumbnail && character.thumbnail.path !== 'image_not_found');
                        // Initialize filtered characters with all characters
                        this.filteredCharacters = this.characters;
                        // Set loading state to false
                        this.isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching characters:', error);
                    });
            },
            fetchSeriesWithCharacters() {
                const seriesPromises = [];
                // Create promises for fetching series in batches
                for (let i = 0; i < this.totalSeries; i += this.seriesBatchSize) {
                    seriesPromises.push(this.fetchSeriesBatch(i));
                }
                // Wait for all promises to resolve
                Promise.all(seriesPromises).then(results => {
                    // Flatten the results array to get all series
                    const allSeries = results.flat();
                    // Store all series
                    this.seriesList = allSeries;
                    // Filter series to include only those with characters
                    this.filteredSeriesList = allSeries.filter(series => series.hasCharacters);
                    // Set loading state to false
                    this.isLoadingSeries = false;
                }).catch(error => {
                    console.error('Error fetching series with characters:', error);
                    // Set loading state to false in case of error
                    this.isLoadingSeries = false;
                });
            },
            fetchSeriesBatch(offset) {
                return axios.get(`https://gateway.marvel.com/v1/public/series?offset=${offset}&limit=${this.seriesBatchSize}&ts=1&apikey=8f852f8f9626b398d68a9b0c1d614afa&hash=586424658aeb66500b1f02216339670b`)
                    .then(response => {
                        const seriesList = response.data.data.results;
                        // Create promises for fetching characters for each series
                        const seriesWithCharactersPromises = seriesList.map(series =>
                            axios.get(`https://gateway.marvel.com/v1/public/series/${series.id}/characters?ts=1&apikey=8f852f8f9626b398d68a9b0c1d614afa&hash=586424658aeb66500b1f02216339670b`)
                                .then(res => ({ ...series, hasCharacters: res.data.data.results.length > 0 }))
                        );
                        return Promise.all(seriesWithCharactersPromises);
                    });
            },
            filterCharacters() {
                // Set loading state to true
                this.isLoadingFilteredCharacters = true;
                // Filter characters based on selected series
                this.filteredCharacters = this.characters.filter(character => {
                    // Check if the character matches the selected series
                    const matchesSeries = this.filters.series.length === 0 || (character.series && character.series.items && character.series.items.some(series =>
                        this.filters.series.includes(series.resourceURI.split('/').pop())
                    ));
                    return matchesSeries;
                });

                // Show the reset button if any filters are applied
                this.showResetButton = this.filters.series.length > 0;

                // If no characters match the filters and series filter is applied, fetch characters by series
                if (this.filteredCharacters.length === 0 && this.filters.series.length > 0) {
                    this.filters.series.forEach(seriesId => {
                        this.fetchCharactersBySeries(seriesId);
                    });
                } else {
                    // Set loading state to false
                    this.isLoadingFilteredCharacters = false;
                }
            },
            fetchCharactersBySeries(seriesId) {
                axios.get(`https://gateway.marvel.com/v1/public/series/${seriesId}/characters?ts=1&apikey=8f852f8f9626b398d68a9b0c1d614afa&hash=586424658aeb66500b1f02216339670b`)
                    .then(response => {
                        // Concatenate fetched characters to the filtered characters array
                        this.filteredCharacters = this.filteredCharacters.concat(response.data.data.results.filter(character => character.thumbnail && character.thumbnail.path !== 'image_not_found'));
                        // Set loading state to false
                        this.isLoadingFilteredCharacters = false;
                    })
                    .catch(error => {
                        console.error('Error fetching characters by series:', error);
                    });
            },
            // Reset all filters and show all characters
            resetFilters() {
                // Reset all filters to empty arrays
                this.filters.series = [];
                // Show all characters
                this.filteredCharacters = this.characters;
                // Hide the reset button
                this.showResetButton = false;
                // Reset the current page to 1
                this.currentPage = 1;
            },
            // Fetch the previous page of characters
            previousPage() {
                // Check if the current page is greater than 1
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },
            // Fetch the next page of characters
            nextPage() {
                // Check if the current page is less than the total number of pages
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },
        },
        computed: {
            // Get the list of characters for the current page
            paginatedCharacters() {
                // Calculate the start and end index of the current page
                const start = (this.currentPage - 1) * this.charactersPerPage;
                const end = start + this.charactersPerPage;
                // Return the paginated characters
                return this.filteredCharacters.slice(start, end);
            },
            // Calculate the total number of pages based on the number of filtered characters and the number of characters per page
            totalPages() {
                return Math.ceil(this.filteredCharacters.length / this.charactersPerPage);
            }
        },
        watch: {
            // Watch for changes in the series filters and update the filtered characters
            'filters.series': {
                handler() {
                    // Call the filterCharacters method when the filters change
                    this.filterCharacters();
                    // Reset the current page to 1 when the filters change
                    this.currentPage = 1;
                },
                // Deep watch to detect changes in nested properties of the filters object
                deep: true
            }
        }
    };
</script>

<style scoped>
    .characters {
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

    .characters-title {
        margin-right: 790px;
    }

    .content {
        display: flex;
        justify-content: space-between;
    }

    .character-list {
        flex: 3;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        grid-gap: 20px;
    }

    .character-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
        border-radius: 10px;
        transition: transform 0.2s;
    }

    .character-card:hover {
        transform: scale(1.05);
    }

    .character-image {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    .character-name {
        margin-top: 10px;
        font-size: 1.2em;
        text-align: center;
        color: #333;
        text-decoration: none;
        transition: color 0.2s;
    }

    .character-name:hover {
        color: #CA8A04;
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
        .character-list {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
    }

    @media (max-width: 480px) {
        .character-list {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        }
    }

    @media (max-width: 320px) {
        .character-list {
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        }
    }
</style>