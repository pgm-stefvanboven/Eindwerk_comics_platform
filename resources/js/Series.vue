<template>
    <div>
        <Header />
        <div class="serie-cards">
            <h1>Marvel Series</h1>

            <div class="serie-list">
                <div v-for="serie in series" :key="serie.id" class="serie-card">
                    <img :src="serie.thumbnail.path + '.' + serie.thumbnail.extension" :alt="serie.title"
                        class="serie-image">
                    <div class="serie-details">
                        <h3 class="serie-title">{{ serie.title }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Header from './vue-components/Header.vue';

export default {
    components: {
        Header
    },
    data() {
        return {
            series: []
        };
    },
    created() {
        this.fetchSeries();
    },
    methods: {
        fetchSeries() {
            axios.get('https://gateway.marvel.com/v1/public/series?ts=1&apikey=9446f8eb6e1702835dbb961d763f4401&hash=b1f7a0387b6770554c0768bb48ac02c1')
                .then(response => {
                    this.series = response.data.data.results;
                })
                .catch(error => {
                    console.error('Error fetching series:', error);
                });
        }
    }
};
</script>

<style scoped>
.serie-cards {
    max-width: 1536px;
    margin: 0 auto;
    padding: 20px;
}

.serie-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.serie-card {
    background-color: #f0f0f0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}

.serie-card:hover {
    transform: scale(1.05);
}

.serie-image {
    width: 100%;
    height: 79%;
}

.serie-details {
    padding: 20px;
}

.serie-title {
    margin-top: 0;
}
</style>