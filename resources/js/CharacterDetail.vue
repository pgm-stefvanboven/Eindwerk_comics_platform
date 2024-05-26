<template>
    <div>
        <Header />
        <h1>{{ character.name }}</h1>
        <img :src="character.thumbnail.path + '.' + character.thumbnail.extension" :alt="character.name" />
        <p>{{ character.description }}</p>
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
        props: {
            id: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                character: null,
                isLoading: true
            };
        },
        created() {
            this.fetchCharacter();
        },
        methods: {
            fetchCharacter() {
                axios.get(`https://gateway.marvel.com/v1/public/characters/${this.id}?ts=1&apikey=8f852f8f9626b398d68a9b0c1d614afa&hash=586424658aeb66500b1f02216339670b`)
                    .then(response => {
                        this.character = response.data.data.results[0];
                        this.isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching character:', error);
                    });
            }
        }
    };
</script>