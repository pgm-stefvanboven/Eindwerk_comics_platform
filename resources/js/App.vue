<template>
    <router-view></router-view>
    <div class="comic-cards">
        <h1>Marvel Comics</h1>
        <div class="comic-list">
            <div v-for="comic in comics" :key="comic.id" class="comic-card">
                <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title"
                    class="comic-image">
                <div class="comic-details">
                    <h3 class="comic-title">{{ comic.title }}</h3>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// Importeer Axios
import axios from 'axios';

export default {
    data() {
        return {
            comics: [] // Array om de resultaten van de API-oproep op te slaan
        };
    },
    created() {
        this.fetchComics(); // Roep de methode aan om de resultaten op te halen wanneer het component is gemaakt
    },
    methods: {
        fetchComics() {
            // Voer een API-aanroep uit om de gegevens op te halen
            axios.get('https://gateway.marvel.com/v1/public/comics?ts=1&apikey=9446f8eb6e1702835dbb961d763f4401&hash=b1f7a0387b6770554c0768bb48ac02c1')
                .then(response => {
                    // Wijs de ontvangen resultaten toe aan de 'comics'-array
                    this.comics = response.data.data.results;
                })
                .catch(error => {
                    console.error('Error fetching Marvel comics:', error);
                });
        }
    }
};
</script>

<style scoped>
.comic-cards {
    max-width: 1536px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    font-size: 2rem;
    margin-bottom: 20px;
}

.comic-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.comic-card {
    background-color: #f0f0f0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.comic-card:hover {
    transform: scale(1.05);
}

.comic-image {
    width: 100%;
    height: 79%;
}

.comic-details {
    padding: 20px;
}

.comic-title {
    margin-top: 0;
}

.comic-description {
    margin-bottom: 0;
}
</style>