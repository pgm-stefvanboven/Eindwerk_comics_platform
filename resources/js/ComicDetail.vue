<template>
    <div>
        <Header />
        <div v-if="comic">
            <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title" class="comic-image">
            <div class="comic-info">
                <h2>{{ comic.title }}</h2>
                <p v-if="comic.pageCount">Aantal pagina's: {{ comic.pageCount }}</p>
                <p v-if="comic.dates && comic.dates.length > 0">Uitgegeven op: {{ comic.dates[0].date }}</p>
                <p v-if="comic.creators && comic.creators.items.length > 0">Gemaakt door:
                    <span v-for="(creator, index) in comic.creators.items">
                        {{ creator.name }}{{ index < comic.creators.items.length - 1 ? ', ' : '' }}
                    </span>
                </p>
            </div>
        </div>
        <div v-else-if="error">
            <p>{{ error }}</p>
        </div>
        <div v-else>
            <p>Loading...</p>
        </div>
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
    props: ['id'],
    data() {
        return {
            comic: null,
            error: null
        };
    },
    created() {
        console.log('Comic ID:', this.id);  // Controleer of de id correct wordt doorgegeven
        this.fetchComicDetails();
    },
    methods: {
        async fetchComicDetails() {
            try {
                const response = await axios.get(`https://gateway.marvel.com/v1/public/comics/${this.id}?ts=1&apikey=e8d09a0b604fd41537ada8adabcf6b4b&hash=c7a4fd72acd5f90bf5b877329faea471`);
                if (response.data && response.data.data && response.data.data.results.length > 0) {
                    this.comic = response.data.data.results[0];
                } else {
                    this.error = 'Comic niet gevonden.';
                }
            } catch (error) {
                console.error('Error fetching comic details:', error);
                this.error = 'Er is een fout opgetreden bij het ophalen van de comic gegevens.';
            }
        }
    }
};
</script>

<style scoped>

</style>