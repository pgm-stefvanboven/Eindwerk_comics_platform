<template>
    <div>
        <Header />
        <div v-if="comic">
            <h2>{{ comic.title }}</h2>
            <img :src="comic.thumbnail.path + '.' + comic.thumbnail.extension" :alt="comic.title" class="comic-image">
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
                const response = await axios.get(`https://gateway.marvel.com/v1/public/comics/${this.id}?ts=1&apikey=9446f8eb6e1702835dbb961d763f4401&hash=b1f7a0387b6770554c0768bb48ac02c1`);
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