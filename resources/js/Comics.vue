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
                            :reduce="character => character.id" :multiple="true" @input="filterComics" />
                    </div>
                    <div class="filter">
                        <label for="series">Series</label>
                        <v-select id="series" v-model="filters.series" :options="seriesList" label="title"
                            :reduce="series => series.id" :multiple="true" @input="filterComics" />
                    </div>
                    <div class="filter">
                        <label for="writers">Writers</label>
                        <v-select id="writers" v-model="filters.writers" :options="writers" label="fullName"
                            :reduce="writer => writer.id" :multiple="true" @input="filterComics" />
                    </div>

                    <button v-if="showResetButton" @click="resetFilters" class="reset-filter-comics-button">Reset
                        Filters</button>
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
                        <div class="comics-img-container">
                            <div class="circular-heart" @click="toggleWishlist(comic)"
                                :class="{ 'wishlist-added': isInWishlist(comic) }">
                                <i class="ri-heart-line icon"></i>
                            </div>
                            <router-link :to="{ name: 'comic', params: { id: comic.id } }" class="comic-title">
                                <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title">
                            </router-link>
                        </div>
                        <router-link :to="{ name: 'comic', params: { id: comic.id } }" class="comic-title">
                            {{ comic.title }}
                        </router-link>
                    </div>
                </div>
            </div>

            <div class="pagination-comics">
                <button @click="previousPage" :class="{ disabled: currentPage === 1 }"
                    :disabled="currentPage === 1">Vorige</button>
                <span class="pages">Pagina {{ currentPage }} van {{ totalPages }}</span>
                <button @click="nextPage" :class="{ disabled: currentPage === totalPages }"
                    :disabled="currentPage === totalPages" class="next">Volgende</button>
            </div>
        </section>
        <Footer />
        <div v-if="showPopup" class="popup">
            <p class="popup-text">{{ popupMessage }}</p>
            <a href="/wishlist" class="button">Ga naar wishlist</a>
            <button @click="showPopup = false">Sluiten</button>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import Header from './vue-components/Header.vue';
    import Footer from './vue-components/Footer.vue';
    import vSelect from 'vue-select';

    export default {
        components: {
            Header,
            Footer,
            vSelect
        },
        data() {
            return {
                comics: [], // Array to store all comics
                isLoading: true, // Boolean to indicate loading state
                isLoadingFilteredComics: false, // Boolean to indicate loading state for filtered comics
                filteredComics: [], // Array to store filtered comics
                characters: [], // Array to store characters
                seriesList: [], // Array to store series
                writers: [], // Array to store writers
                currentPage: 1, // Current page number for pagination
                comicsPerPage: 10, // Number of comics to display per page
                filters: {
                    characters: [], // Selected characters for filtering
                    series: [], // Selected series for filtering
                    writers: [] // Selected writers for filtering
                },
                showPopup: false, // Boolean to control the display of the popup message
                popupMessage: '', // Message to display in the popup
                showResetButton: false // Boolean to control the visibility of the reset button
            };
        },
        created() {
            this.fetchComics(); // Fetch all comics when the component is created
            this.fetchCharacters(); // Fetch all characters when the component is created
            this.fetchSeries(); // Fetch all series when the component is created
            this.fetchWriters(); // Fetch all writers when the component is created
        },
        methods: {
            fetchComics() {
                axios.get('https://gateway.marvel.com/v1/public/comics?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471')
                    .then(response => {
                        this.comics = response.data.data.results.filter(comic => comic.thumbnail && comic.thumbnail.path !== 'image_not_found');
                        this.filteredComics = this.comics; // Initialize filtered comics with all comics
                        this.isLoading = false; // Set loading state to false
                        console.log("Comics fetched:", this.comics); // Debugging log
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
            fetchFilteredComics() {
                // Prepare character filter query string if characters filter is applied
                const characterFilter = this.filters.characters.length ? `characters=${this.filters.characters.join(',')}&` : '';
                // Prepare series filter query string if series filter is applied
                const seriesFilter = this.filters.series.length ? `series=${this.filters.series.join(',')}&` : '';
                // Prepare writers filter query string if writers filter is applied
                const writersFilter = this.filters.writers.length ? `creators=${this.filters.writers.join(',')}&` : '';

                // Combine all filter query strings
                const filterQuery = characterFilter + seriesFilter + writersFilter;

                // Make a GET request to the Marvel API with the combined filters
                axios.get(`https://gateway.marvel.com/v1/public/comics?${filterQuery}ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471`)
                    .then(response => {
                        // Filter the response to exclude comics with missing or "image_not_found" thumbnails
                        this.filteredComics = response.data.data.results.filter(comic => comic.thumbnail && comic.thumbnail.path !== 'image_not_found');
                        // Set loading state to false
                        this.isLoadingFilteredComics = false;
                    })
                    .catch(error => {
                        // Log any errors to the console
                        console.error('Error fetching filtered comics:', error);
                        // Set loading state to false in case of error
                        this.isLoadingFilteredComics = false;
                    });
            },

            filterComics() {
                // Set loading state to true
                this.isLoadingFilteredComics = true;
                // Filter the comics based on the selected characters, series, and writers
                this.filteredComics = this.comics.filter(comic => {
                    // Check if the comic matches the selected characters
                    const matchesCharacters = this.filters.characters.length === 0 || comic.characters.items.some(character =>
                        this.filters.characters.includes(character.resourceURI.split('/').pop())
                    );
                    // Check if the comic matches the selected series
                    const matchesSeries = this.filters.series.length === 0 || this.filters.series.includes(comic.series.resourceURI.split('/').pop());
                    // Check if the comic matches the selected writers
                    const matchesWriters = this.filters.writers.length === 0 || comic.creators.items.some(creator =>
                        this.filters.writers.includes(creator.resourceURI.split('/').pop())
                    );
                    // Return true if the comic matches all selected filters
                    return matchesCharacters && matchesSeries && matchesWriters;
                });

                // Show the reset button if any filters are applied
                this.showResetButton = this.filters.characters.length > 0 || this.filters.series.length > 0 || this.filters.writers.length > 0;

                // If no comics match the filters and characters filter is applied, fetch comics by characters
                if (this.filteredComics.length === 0) {
                    this.fetchFilteredComics();
                } else {
                    // Set loading state to false
                    this.isLoadingFilteredComics = false;
                }
            },
            // Reset all filters and show all comics
            resetFilters() {
                // Reset all filters to empty arrays
                this.filters.characters = [];
                // Reset all filters to empty arrays
                this.filters.series = [];
                // Reset all filters to empty arrays
                this.filters.writers = [];
                // Show all comics
                this.filteredComics = this.comics;
                // Hide the reset button
                this.showResetButton = false;
                // Reset the current page to 1
                this.currentPage = 1;
            },
            // Fetch the previous page of comics
            previousPage() {
                // Check if the current page is greater than 1
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },
            // Fetch the next page of comics
            nextPage() {
                // Check if the current page is less than the total number of pages
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },
            // Check if a comic is in the wishlist
            isInWishlist(comic) {
                // Get the wishlist from local storage or an empty array if it doesn't exist
                const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                // Check if the comic is in the wishlist
                return wishlist.some(item => item.id === comic.id);
            },
            // Add or remove a comic from the wishlist
            toggleWishlist(comic) {
                // Get the wishlist from local storage or an empty array if it doesn't exist
                const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
                // Find the index of the comic in the wishlist
                const comicIndex = wishlist.findIndex(item => item.id === comic.id);
                // If the comic is in the wishlist, remove it; otherwise, add it
                if (comicIndex !== -1) {
                    wishlist.splice(comicIndex, 1);
                    this.popupMessage = `${comic.title} removed from wishlist.`; // Show removed message
                } else {
                    wishlist.push(comic);
                    this.popupMessage = `${comic.title} added to wishlist.`; // Show added message
                }
                // Save the updated wishlist to local storage
                localStorage.setItem('wishlist', JSON.stringify(wishlist));
                this.showPopup = true; // Show the popup message
            }
        },
        // Computed properties for pagination and filtering
        computed: {
            // Get the list of comics for the current page
            paginatedComics() {
                // Calculate the start and end index of the current page
                const start = (this.currentPage - 1) * this.comicsPerPage;
                const end = start + this.comicsPerPage;
                // Return the comics for the current page
                return this.filteredComics.slice(start, end);
            },
            // Calculate the total number of pages based on the number of filtered comics and the number of comics per page
            totalPages() {
                return Math.ceil(this.filteredComics.length / this.comicsPerPage);
            }
        },
        // Watch for changes in the filters and update the filtered comics
        watch: {
            filters: {
                handler() {
                    // Call the filterComics method when the filters change
                    this.filterComics();
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
        margin-right: 830px;
    }

    .content {
        display: flex;
        justify-content: space-between;
    }

    .comic-list {
        flex: 3;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }

    .comic-card {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .comics-img-container {
        position: relative;
        width: 100%;
        padding-top: 150%;
        overflow: hidden;
        border-radius: 10px;
        transition: box-shadow 0.3s;
    }

    .comics-img-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 10px;
        transition: transform 0.3s;
    }

    .comics-img-container:hover img {
        transform: scale(1.05);
    }

    .circular-heart {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: transform 0.2s;
        z-index: 10;
    }

    .circular-heart:hover {
        transform: scale(1.1);
    }

    .wishlist-added .icon {
        color: black;
    }

    .wishlist-added {
        color: #d63031;
        background-color: #d63031;
    }

    .icon {
        font-size: 20px;
    }

    .comic-title {
        margin-top: 10px;
        font-size: 1.2em;
        text-align: center;
        font-weight: bold;
    }

    .filters {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
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

    .pagination-comics {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        max-width: 1070px;
        margin-left: 400px;
    }

    .pagination-comics .pages {
        margin: 0 20px;
        flex-grow: 1;
        text-align: center;
    }

    .pagination-comics button {
        padding: 10px 20px;
        cursor: pointer;
    }

    .pagination-comics button.disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    button {
        background-color: #CA8A04;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        transition-duration: 0.4s;
        border-radius: 25px;
    }

    .button {
        background-color: #CA8A04;
        border: none;
        color: white;
        padding: 5px 15px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        cursor: pointer;
        transition-duration: 0.4s;
        border-radius: 25px;
        margin-right: 10px;
    }

    .next,
    .pages {
        margin-left: 10px;
    }

    button:hover {
        background-color: #FFD700;
    }

    .button:hover {
        background-color: #FFD700;
    }

    .reset-filter-comics-button {
        background-color: #CA8A04;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        cursor: pointer;
        transition-duration: 0.4s;
        border-radius: 25px;
        margin-right: 10px;
        margin-top: 20px;
    }

    .reset-filter-comics-button:hover {
        background-color: #FFD700;
    }

    @media (max-width: 768px) {
        .comic-list {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }

        .filters {
            margin-right: 0;
            margin-bottom: 20px;
        }

        .titles {
            flex-direction: column;
            align-items: flex-start;
        }

        .comics-title {
            text-align: left;
            margin-right: 0;
        }

        .content {
            flex-direction: column;
        }

        .pagination-comics {
            margin-right: 0;
        }

        .pagination-comics button {
            margin: 0 5px;
        }

        .next,
        .pages {
            margin-left: 5px;
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

    .popup {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        z-index: 1000;
    }

    .popup button {
        background-color: transparent;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 16px;
    }
</style>